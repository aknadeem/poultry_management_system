<?php

namespace App\Actions\ProductManagement;

use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class StoreProductAction
{
    use MapsProductFormData;

    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?object $pictureFile, int $userId): Product
    {
        return DB::transaction(function () use ($data, $pictureFile, $userId) {
            $attributes = $this->mapProductAttributes($data);
            $attributes['product_picture'] = $this->uploadService->store($pictureFile, 'products');
            $attributes['addedby'] = $userId;

            return Product::create($attributes);
        });
    }
}
