<?php

namespace App\Models;

use App\Support\PostgresSchema;
use Illuminate\Database\Eloquent\Model;

class CoreUser extends Model
{
    protected $table = 'users';

    protected $guarded = [];

    public function getTable(): string
    {
        $table = $this->table;

        if (!PostgresSchema::usesPgsql()) {
            return $table;
        }

        return PostgresSchema::qualify(PostgresSchema::core(), $table);
    }
}
