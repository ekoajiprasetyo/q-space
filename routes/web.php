<?php

use Illuminate\Support\Facades\Route;

$shortLinkDomain = env('SHORTLINK_DOMAIN', 's.q-link.my.id');
$appDomain = env('APP_DOMAIN', 'space.q-link.my.id');

/*
|--------------------------------------------------------------------------
| Short Link Domain Routes (s.q-link.my.id)
|--------------------------------------------------------------------------
|
| These routes are only accessible via the short link domain.
| It handles the root redirect (to main app) and the short code redirection.
|
*/
Route::domain($shortLinkDomain)->group(function () {
    Route::get('/', function () {
        abort(404);
    });

    // Catch-all for short codes
    Route::get('/{code}', [\App\Http\Controllers\ShortLinkController::class, 'redirect'])->name('short_link.redirect');
});

/*
|--------------------------------------------------------------------------
| Main Application Routes (space.q-link.my.id)
|--------------------------------------------------------------------------
|
| These routes serve the main application logic: Dashboard, Files, Crews, etc.
| Wrapped in a domain group to prevent access from the short link domain.
| Change APP_DOMAIN in .env for local development (e.g., localhost).
|
*/
Route::domain($appDomain)->group(function () {
    
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::middleware('auth')->group(function () {
        // Files (Q-Store)
        Route::get('/files', [\App\Http\Controllers\FileRequestController::class, 'index'])->name('files.index');
        Route::resource('file-requests', \App\Http\Controllers\FileRequestController::class)->only(['create', 'store', 'show', 'destroy']);
        Route::post('/file-requests/{fileRequest}/toggle', [\App\Http\Controllers\FileRequestController::class, 'toggleStatus'])->name('file-requests.toggle');
        Route::delete('/file-requests/{fileRequest}/submissions', [\App\Http\Controllers\FileRequestController::class, 'destroySubmission'])->name('file-requests.submissions.destroy');
        Route::post('/file-requests/{fileRequest}/upload-tasks/{uploadTask}/retry', [\App\Http\Controllers\FileRequestController::class, 'retryUploadTask'])->name('file-requests.upload-tasks.retry');

        // Paths (Short Links Management)
        Route::resource('paths', \App\Http\Controllers\ShortLinkController::class)->only(['index', 'store', 'destroy', 'update']);

        // Codes (QR Generator)
        Route::get('/codes', [\App\Http\Controllers\QrCodeController::class, 'index'])->name('codes.index');
        Route::post('/codes/dynamic', [\App\Http\Controllers\QrCodeController::class, 'storeDynamic'])->name('codes.dynamic');
        Route::post('/codes/text', [\App\Http\Controllers\QrTextController::class, 'store'])->name('qr-text.store');
        Route::delete('/codes/text/{qrText}', [\App\Http\Controllers\QrTextController::class, 'destroy'])->name('qr-text.destroy');

        // Crews (Group Generator)
        Route::get('/crews', [\App\Http\Controllers\CrewsController::class, 'index'])->name('crews.index');

    });

    Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('auth.google.callback');

    // Public Upload Link
    Route::get('/upload/{slug}', [\App\Http\Controllers\FileRequestController::class, 'publicUpload'])->name('file-requests.upload');
    Route::post('/upload/{slug}', [\App\Http\Controllers\FileRequestController::class, 'storePublicUpload'])->name('file-requests.upload.store');
    Route::get('/queue/trigger', [\App\Http\Controllers\PublicQueueRunnerController::class, 'trigger'])
        ->middleware(['signed', 'throttle:30,1'])
        ->name('queue.trigger');

    // QR Text View (Public)
    Route::get('/t/{slug}', [\App\Http\Controllers\QrTextController::class, 'show'])->name('qr-text.show');

    require __DIR__.'/auth.php';
});
