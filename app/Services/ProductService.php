<?php

namespace App\Services;

use App\Actions\CreateProductAction;
use App\Actions\UpdateProductAction;
use App\Mail\PriceChangeNotification;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class ProductService
{
    public function __construct(
        private CreateProductAction $createProductAction,
        private UpdateProductAction $updateProductAction
    ) {}

    /**
     * Creates a new product.
     *
     * @param  string  $name  The name of the product.
     * @param  float  $price  The price of the product.
     * @param  string  $description  A brief description of the product.
     * @param  UploadedFile|null  $image  An optional image file for the product.
     * @return Product The newly created product instance.
     */
    public function create(string $name, float $price, ?string $description = null, ?UploadedFile $image = null): Product
    {
        return $this->createProductAction->execute($name, $price, $description, $image);
    }

    /**
     * Updates an existing product and notifies the admin if the price has changed.
     *
     * @param  Product  $product  The product instance to update.
     * @param  string  $name  The new name of the product.
     * @param  float  $price  The new price of the product.
     * @param  string  $description  The updated description of the product.
     * @param  UploadedFile|null  $image  An optional new image for the product.
     * @return Product The updated product instance.
     */
    public function update(Product $product, string $name, float $price, ?string $description = null, ?UploadedFile $image = null): Product
    {
        $oldPrice = $product->price;
        $product = $this->updateProductAction->execute($product, $name, (float) $price, $description, $image);
        $this->sendPriceChangedEmailToAdmin($oldPrice, $product->price, $product->name);

        return $product;
    }

    /**
     * Sends an email notification to the admin when a product's price is updated.
     *
     * @param  float  $oldPrice  The previous price of the product.
     * @param  float  $newPrice  The updated price of the product.
     * @param  string  $name  The name of the product.
     */
    private function sendPriceChangedEmailToAdmin(float $oldPrice, float $newPrice, string $name): void
    {
        Mail::to(config('mail.to.admin.address'))
            ->queue(new PriceChangeNotification(
                $name,
                $oldPrice,
                $newPrice
            ));
    }
}
