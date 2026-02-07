<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'original_url',
        'short_code',
        'visits',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
