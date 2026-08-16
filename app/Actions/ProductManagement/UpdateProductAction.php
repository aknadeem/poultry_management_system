<?php

namespace App\Actions\ProductManagement;

use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    use MapsProductFormData;

    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Product $product, array $data, ?object $pictureFile, int $userId): Product
    {
        return DB::transaction(function () use ($product, $data, $pictureFile, $userId) {
            $attributes = $this->mapProductAttributes($data);
            $attributes['product_picture'] = $this->uploadService->replace(
                $pictureFile,
                'products',
                $product->product_picture
            );
            $attributes['updatedby'] = $userId;

            $product->update($attributes);

            return $product->fresh();
        });
    }
}
