<?php

namespace App\Models;

use App\Support\PostgresSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'teacher_core_user_id',
        'class_id',
        'title',
        'slug',
        'description',
        'allowed_extensions',
        'max_file_size',
        'max_files',
        'deadline',
        'google_drive_folder_id',
        'is_active',
    ];

    protected $casts = [
        'allowed_extensions' => 'array',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeOwnedByTeacherIdentity(Builder $query, int $identityId): Builder
    {
        if (!PostgresSchema::usesPgsql()) {
            return $query->where('teacher_id', $identityId);
        }

        return $query->where(function (Builder $builder) use ($identityId) {
            $builder->where('teacher_core_user_id', $identityId)
                ->orWhere(function (Builder $fallback) use ($identityId) {
                    $fallback->whereNull('teacher_core_user_id')
                        ->where('teacher_id', $identityId);
                });
        });
    }

    public function ownerMatches(int $identityId): bool
    {
        if ($this->teacher_core_user_id !== null) {
            return (int) $this->teacher_core_user_id === $identityId;
        }

        return (int) $this->teacher_id === $identityId;
    }

    public static function ownerAttributes(int $identityId): array
    {
        $attributes = ['teacher_id' => $identityId];

        if (PostgresSchema::usesPgsql()) {
            $attributes['teacher_core_user_id'] = $identityId;
        }

        return $attributes;
    }

    public function ownerIdentityId(): int
    {
        return (int) ($this->teacher_core_user_id ?? $this->teacher_id);
    }

    public function teacherCoreUser(): BelongsTo
    {
        return $this->belongsTo(CoreUser::class, 'teacher_core_user_id');
    }

    public function getGoogleDriveFolderUrlAttribute(): string
    {
        return 'https://drive.google.com/drive/folders/' . $this->google_drive_folder_id;
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FileSubmission::class);
    }

    public function uploadTasks(): HasMany
    {
        return $this->hasMany(UploadTask::class);
    }
}
