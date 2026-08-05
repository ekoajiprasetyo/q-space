<?php

namespace App\Models;

use App\Support\PostgresSchema;
use Illuminate\Database\Eloquent\Model;

class CoreClassRoom extends Model
{
    protected $table = 'classes';

    protected $guarded = [];

    public function getTable(): string
    {
        if (! PostgresSchema::usesPgsql()) {
            return $this->table;
        }

        return PostgresSchema::qualify(PostgresSchema::core(), $this->table);
    }
}
