<?php

namespace App\Http\Controllers;

use App\Models\UploadTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PublicQueueRunnerController extends Controller
{
    public function trigger(Request $request): JsonResponse
    {
        $runs = max(1, min((int) $request->query('runs', 1), 3));

        $lock = Cache::lock('public-upload-queue-runner', 300);
        if (!$lock->get()) {
            return response()->json([
                'ok' => false,
                'message' => 'Queue runner is busy',
            ], 429);
        }

        try {
            $processedRuns = 0;
            for ($i = 0; $i < $runs; $i++) {
                Artisan::call('queue:work', [
                    'connection' => 'database',
                    '--queue' => 'uploads,default',
                    '--once' => true,
                    '--tries' => 3,
                    '--timeout' => 1800,
                    '--sleep' => 0,
                ]);
                $processedRuns++;
            }

            $this->cleanupStalePendingUploads();

            return response()->json([
                'ok' => true,
                'processed_runs' => $processedRuns,
            ]);
        } finally {
            $lock->release();
        }
    }

    protected function cleanupStalePendingUploads(): void
    {
        $disk = Storage::disk('local');
        foreach ($disk->allFiles('pending-uploads') as $path) {
            $task = UploadTask::where('staged_path', $path)->first();
            if (!$task) {
                if ($disk->lastModified($path) < now()->subDays(2)->timestamp) {
                    $disk->delete($path);
                }
                continue;
            }

            if ($task->status === 'uploaded') {
                $disk->delete($path);
                continue;
            }

            // Keep failed file for manual retry. Delete only very old failed leftovers.
            if ($task->status === 'failed' && $disk->lastModified($path) < now()->subDays(7)->timestamp) {
                $disk->delete($path);
            }
        }
    }
}
