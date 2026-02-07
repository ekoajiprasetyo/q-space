<?php

namespace App\Models;

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

    public function getUrlAttribute(): string
    {
        return route('qr-text.show', $this->slug);
    }
}
