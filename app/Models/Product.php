<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public static function cachedFindOrFail(int $id): self
    {
        return Cache::remember("product:{$id}", now()->addMinutes(10), function () use ($id) {
            return self::findOrFail($id);
        });
    }

}
