<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaticQrCode extends Model
{
    protected $fillable = ['user_id', 'name', 'content', 'options', 'fingerprint'];

    protected function casts(): array
    {
        return ['options' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedByIdentity(Builder $query, int $identityId): Builder
    {
        return $query->where('user_id', $identityId);
    }

    public static function ownerAttributes(int $identityId): array
    {
        return ['user_id' => $identityId];
    }

    public function ownerMatches(int $identityId): bool
    {
        return (int) $this->user_id === $identityId;
    }
}
