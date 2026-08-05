<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrText extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'content',
        'theme',
        'views',
        'is_active',
        'qr_options',
    ];

    protected $casts = [
        'is_active' => 'boolean',
            'views' => 'integer',
            'qr_options' => 'array',
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
        return $query->where('user_id', $identityId);
    }

    public function ownerMatches(int $identityId): bool
    {
        return (int) $this->user_id === $identityId;
    }

    public static function ownerAttributes(int $identityId): array
    {
        return ['user_id' => $identityId];
    }

    public function getUrlAttribute(): string
    {
        return sprintf('https://%s/t/%s', config('app.shortlink_domain'), $this->slug);
    }
}
