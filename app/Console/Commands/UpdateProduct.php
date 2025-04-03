<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;

class UpdateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update {id} {--name=} {--description=} {--price=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a product with the specified details';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private ProductService $productService
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        try {
            $product = Product::findOrFail($id);
        } catch (\Throwable $th) {
            $this->error('Product not found !');
            return 1;
        }
        

        $data = [];
        if ($this->option('name')) {
            $data['name'] = $this->option('name');
        }
        if ($this->option('description')) {
            $data['description'] = $this->option('description');
        }
        if ($this->option('price')) {
            $data['price'] = $this->option('price');
        }

        $rules = [
            'name' => 'nullable|string|min:3',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()));
            return 1;
        }

        if (! empty($data)) {
            $this->productService->update($product, $data['name'] ?? $product->name, $data['price'] ?? $product->price, $data['description'] ?? $product->description, null);

            $this->info('Product updated successfully.');
        } else {
            $this->info('No changes provided. Product remains unchanged.');
        }

        return 0;
    }
}
