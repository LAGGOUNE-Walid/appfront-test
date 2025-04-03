<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
    ];

    public static function cachedFindOrFail(int $id): self
    {
        return Cache::remember("product:{$id}", now()->addMinutes(10), function () use ($id) {
            return self::findOrFail($id);
        });
    }
}
