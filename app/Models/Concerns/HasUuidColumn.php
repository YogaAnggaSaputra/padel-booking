<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuidColumn
{
    public static function bootHasUuidColumn(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
