<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_request_id',
        'teacher_id',
        'submitter_name',
        'class_name',
        'student_notes',
        'original_filename',
        'mime_type',
        'file_size',
        'staged_path',
        'student_folder_id',
        'status',
        'attempts',
        'last_error',
        'google_drive_file_id',
        'google_drive_url',
        'queued_at',
        'processed_at',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function fileRequest(): BelongsTo
    {
        return $this->belongsTo(FileRequest::class);
    }
}
