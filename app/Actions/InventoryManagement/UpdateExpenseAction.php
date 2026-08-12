<?php

namespace App\Actions\InventoryManagement;

use App\Models\Expense;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class UpdateExpenseAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(Expense $expense, array $data, ?object $imageFile, int $userId): Expense
    {
        return DB::transaction(function () use ($expense, $data, $imageFile, $userId) {
            $imageName = $this->uploadService->replace(
                $imageFile,
                'expenses',
                $expense->picture
            );

            $expense->update([
                'category_id' => $data['category_id'],
                'expense_date' => $data['expense_date'],
                'amount' => $data['amount'],
                'remarks' => $data['remarks'],
                'picture' => $imageName,
                'updatedby' => $userId,
            ]);

            return $expense->fresh();
        });
    }
}
