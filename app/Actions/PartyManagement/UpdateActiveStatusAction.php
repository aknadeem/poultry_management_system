<?php

namespace App\Actions\PartyManagement;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UpdateActiveStatusAction
{
    private const ALLOWED_TABLES = [
        'party_companies',
        'brokers',
        'products',
        'product_sales',
        'product_purchases',
        'product_stores',
        'vaccination_schedules',
        'companies',
    ];

    public function execute(int $id, string $tableName, int $userId): void
    {
        if (! in_array($tableName, self::ALLOWED_TABLES, true) || ! Schema::hasTable($tableName)) {
            throw ValidationException::withMessages([
                'tag' => 'Invalid status target',
            ]);
        }

        $row = DB::table($tableName)->where('id', $id)->first();
        if (! $row) {
            throw ValidationException::withMessages([
                'id' => 'Record not found',
            ]);
        }

        $status = ((int) ($row->is_active ?? 0) === 0) ? 1 : 0;

        DB::table($tableName)->where('id', $id)->update([
            'is_active' => $status,
            'updatedby' => $userId,
        ]);
    }
}
