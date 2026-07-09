<?php

namespace App\Support;

class PostgresSchema
{
    public static function usesPgsql(): bool
    {
        $default = config('database.default');

        return config("database.connections.{$default}.driver") === 'pgsql';
    }

    public static function app(): string
    {
        return config('database.schemas.app', 'public');
    }

    public static function core(): string
    {
        return config('database.schemas.core', 'core');
    }

    public static function qualify(string $schema, string $table): string
    {
        return "{$schema}.{$table}";
    }
}
