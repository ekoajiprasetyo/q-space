<?php

namespace App\Models;

use App\Support\PostgresSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoreStudent extends Model
{
    protected $table = 'students';

    protected $guarded = [];

    public function getTable(): string
    {
        $table = $this->table;

        if (!PostgresSchema::usesPgsql()) {
            return $table;
        }

        return PostgresSchema::qualify(PostgresSchema::core(), $table);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(CoreUser::class, 'user_id');
    }
}
