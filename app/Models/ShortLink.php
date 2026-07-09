<?php

namespace App\Models;

use App\Support\PostgresSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortLink extends Model
{
    protected $fillable = [
        'user_id',
        'core_user_id',
        'name',
        'original_url',
        'short_code',
        'visits',
        'is_active',
    ];

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
}
