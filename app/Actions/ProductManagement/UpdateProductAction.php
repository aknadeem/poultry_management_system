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
        $existingPicture = $product->product_picture;
        $stagedPicture = null;

        try {
            if ($pictureFile) {
                $stagedPicture = $this->uploadService->store($pictureFile, 'products');
            }

            $updated = DB::transaction(function () use ($product, $data, $existingPicture, $stagedPicture, $userId): Product {
                $attributes = $this->mapProductAttributes($data);
                $attributes['product_picture'] = $stagedPicture ?? $existingPicture;
                $attributes['updatedby'] = $userId;

                $product->update($attributes);

                return $product->fresh();
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('products', $stagedPicture);

            throw $exception;
        }

        if ($stagedPicture) {
            $this->uploadService->delete('products', $existingPicture);
        }

        return $updated;
    }
}
