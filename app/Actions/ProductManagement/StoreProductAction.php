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
        $stagedPicture = null;

        try {
            $stagedPicture = $this->uploadService->store($pictureFile, 'products');

            return DB::transaction(function () use ($data, $stagedPicture, $userId): Product {
                $attributes = $this->mapProductAttributes($data);
                $attributes['product_picture'] = $stagedPicture;
                $attributes['addedby'] = $userId;

                return Product::query()->create($attributes);
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('products', $stagedPicture);

            throw $exception;
        }
    }
}
