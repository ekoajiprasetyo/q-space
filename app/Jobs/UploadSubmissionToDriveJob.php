<?php

namespace App\Jobs;

use App\Models\FileSubmission;
use App\Models\UploadTask;
use App\Models\UserGoogleToken;
use App\Services\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadSubmissionToDriveJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $timeout = 1800;
    public array $backoff = [60, 300, 900];

    public function __construct(public int $uploadTaskId)
    {
    }

    public function handle(GoogleDriveService $googleDriveService): void
    {
        $lock = Cache::lock('upload-task-job-' . $this->uploadTaskId, 3600);
        if (!$lock->get()) {
            $this->release(30);
            return;
        }

        try {
        $task = UploadTask::find($this->uploadTaskId);
        if (!$task) {
            return;
        }

        if ($task->status === 'uploaded') {
            return;
        }

        $task->update([
            'status' => 'processing',
            'attempts' => (int) $task->attempts + 1,
            'last_error' => null,
        ]);

        $token = UserGoogleToken::ownedByIdentity($task->ownerIdentityId())->first();
        if (!$token) {
            throw new \RuntimeException('Google token for teacher not found.');
        }

        $task->loadMissing('fileRequest');
        $submittedAt = $task->queued_at ?? $task->created_at ?? now();
        $isLateSubmission = $task->fileRequest?->deadline
            ? $submittedAt->gt($task->fileRequest->deadline)
            : false;

        $absolutePath = Storage::disk('local')->path($task->staged_path);
        if (!is_file($absolutePath)) {
            throw new \RuntimeException('Staged upload file is missing: ' . $task->staged_path);
        }

        $googleDriveService->setAccessToken($token);
        $driveFile = $googleDriveService->uploadLocalFile(
            $absolutePath,
            $task->student_folder_id,
            $task->original_filename,
            $task->mime_type ?: 'application/octet-stream'
        );

        FileSubmission::updateOrCreate([
            'google_drive_file_id' => $driveFile['id'],
        ], [
            'file_request_id' => $task->file_request_id,
            'student_id' => null,
            'submitter_name' => $task->submitter_name,
            'original_filename' => $task->original_filename,
            'google_drive_file_id' => $driveFile['id'],
            'google_drive_url' => $driveFile['url'],
            'file_size' => $task->file_size,
            'mime_type' => $task->mime_type ?: 'application/octet-stream',
            'status' => $isLateSubmission ? 'late' : 'submitted',
            'submitted_at' => $submittedAt,
            'student_notes' => $task->student_notes,
        ]);

        $task->update([
            'status' => 'uploaded',
            'google_drive_file_id' => $driveFile['id'],
            'google_drive_url' => $driveFile['url'],
            'processed_at' => now(),
            'last_error' => null,
        ]);

        Storage::disk('local')->delete($task->staged_path);
        } finally {
            $lock->release();
        }
    }

    public function failed(\Throwable $exception): void
    {
        $task = UploadTask::find($this->uploadTaskId);
        if ($task) {
            $task->update([
                'status' => 'failed',
                'last_error' => mb_substr($exception->getMessage(), 0, 1000),
            ]);
        }

        Log::error('UploadSubmissionToDriveJob failed', [
            'upload_task_id' => $this->uploadTaskId,
            'error' => $exception->getMessage(),
        ]);
    }
}
