<?php

namespace App\Actions\InventoryManagement;

use App\Models\Expense;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DestroyExpenseAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $this->uploadService->delete('expenses', $expense->picture);
            $expense->delete();
        });
    }
}
