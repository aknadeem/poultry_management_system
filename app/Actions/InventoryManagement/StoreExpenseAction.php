<?php

namespace App\Actions\InventoryManagement;

use App\Models\Expense;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class StoreExpenseAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $imageFile, int $userId): Expense
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $imageName = $this->uploadService->store($imageFile, 'expenses');

            return Expense::create([
                'category_id' => $data['category_id'],
                'expense_date' => $data['expense_date'],
                'amount' => $data['amount'],
                'remarks' => $data['remarks'],
                'picture' => $imageName,
                'addedby' => $userId,
            ]);
        });
    }
}
