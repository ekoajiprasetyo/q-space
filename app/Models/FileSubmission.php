<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_request_id',
        'student_id',
        'student_core_student_id',
        'submitter_name',
        'original_filename',
        'google_drive_file_id',
        'google_drive_url',
        'file_size',
        'mime_type',
        'status',
        'student_notes',
        'teacher_notes',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function fileRequest(): BelongsTo
    {
        return $this->belongsTo(FileRequest::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studentCoreStudent(): BelongsTo
    {
        return $this->belongsTo(CoreStudent::class, 'student_core_student_id');
    }
}
