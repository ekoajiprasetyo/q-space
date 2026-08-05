<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortLink extends Model
{
    public const SOURCE_PATH = 'path';
    public const SOURCE_QR_DYNAMIC = 'qr_dynamic';

    protected $fillable = [
        'user_id',
        'name',
        'original_url',
        'short_code',
        'visits',
        'is_active',
        'source',
        'qr_options',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return ['qr_options' => 'array'];
    }

    public function scopeOwnedByIdentity(Builder $query, int $identityId): Builder
    {
        return $query->where('user_id', $identityId);
    }

    public function scopeFromSource(Builder $query, string $source): Builder
    {
        return $query->where('source', $source);
    }

    public function ownerMatches(int $identityId): bool
    {
        return (int) $this->user_id === $identityId;
    }

    public static function ownerAttributes(int $identityId): array
    {
        return ['user_id' => $identityId];
    }
}
