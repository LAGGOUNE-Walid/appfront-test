<?php

namespace App\Actions;

use App\Models\Product;
use App\Traits\HasProductImageUploader;
use Illuminate\Http\UploadedFile;

class UpdateProductAction
{
    use HasProductImageUploader;

    public function execute(Product $product, string $name, float $price, string $description, ?UploadedFile $image = null): Product
    {
        $product->update([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'image' => ($image) ? $this->upload($image) : $product->image,
        ]);

        return $product;
    }
}
