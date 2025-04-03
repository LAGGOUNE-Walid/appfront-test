<?php

namespace App\Actions;

use App\Models\Product;
use App\Traits\HasProductImageUploader;
use Illuminate\Http\UploadedFile;

class CreateProductAction
{
    use HasProductImageUploader;

    public function execute(string $name, float $price, string $description, ?UploadedFile $image = null): Product
    {
        return Product::create([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'image' => $this->upload($image),
        ]);
    }
}
