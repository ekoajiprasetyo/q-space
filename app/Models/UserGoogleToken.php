<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGoogleToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_email',
        'google_name',
        'google_avatar',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

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
}
