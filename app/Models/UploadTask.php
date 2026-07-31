<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'resumable_upload_uri',
        'uploaded_bytes',
        'upload_session_started_at',
        'last_chunk_uploaded_at',
        'queued_at',
        'processed_at',
    ];

    protected $casts = [
        'uploaded_bytes' => 'integer',
        'upload_session_started_at' => 'datetime',
        'last_chunk_uploaded_at' => 'datetime',
        'queued_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function fileRequest(): BelongsTo
    {
        return $this->belongsTo(FileRequest::class);
    }

    public function scopeOwnedByTeacherIdentity(Builder $query, int $identityId): Builder
    {
        return $query->where('teacher_id', $identityId);
    }

    public function ownerMatches(int $identityId): bool
    {
        return (int) $this->teacher_id === $identityId;
    }

    public static function ownerAttributes(int $identityId): array
    {
        return ['teacher_id' => $identityId];
    }

    public function ownerIdentityId(): int
    {
        return (int) $this->teacher_id;
    }
}
