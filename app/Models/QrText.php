<?php

namespace App\Models;

use App\Support\PostgresSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrText extends Model
{
    protected $fillable = [
        'user_id',
        'core_user_id',
        'slug',
        'title',
        'content',
        'theme',
        'views',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::random(8);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedByIdentity(Builder $query, int $identityId): Builder
    {
        if (!PostgresSchema::usesPgsql()) {
            return $query->where('user_id', $identityId);
        }

        return $query->where(function (Builder $builder) use ($identityId) {
            $builder->where('core_user_id', $identityId)
                ->orWhere(function (Builder $fallback) use ($identityId) {
                    $fallback->whereNull('core_user_id')
                        ->where('user_id', $identityId);
                });
        });
    }

    public function ownerMatches(int $identityId): bool
    {
        if ($this->core_user_id !== null) {
            return (int) $this->core_user_id === $identityId;
        }

        return (int) $this->user_id === $identityId;
    }

    public static function ownerAttributes(int $identityId): array
    {
        $attributes = ['user_id' => $identityId];

        if (PostgresSchema::usesPgsql()) {
            $attributes['core_user_id'] = $identityId;
        }

        return $attributes;
    }

    public function coreUser(): BelongsTo
    {
        return $this->belongsTo(CoreUser::class, 'core_user_id');
    }

    public function getUrlAttribute(): string
    {
        return route('qr-text.show', $this->slug);
    }
}
