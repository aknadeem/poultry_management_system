<?php

namespace App\Actions\ProductManagement;

use Carbon\Carbon;

trait MapsProductFormData
{
    /**
     * Map validated create/edit form fields onto product table columns.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mapProductAttributes(array $data): array
    {
        $warrantyPeriod = $data['warranty_period'] ?? null;
        $hasWarranty = $warrantyPeriod !== null && $warrantyPeriod !== '';

        return [
            'party_company_id' => $data['company_id'] ?? null,
            'product_category_id' => $data['product_category_id'] ?? null,
            'product_group' => $data['product_group'] ?? null,
            'product_name' => $data['product_name'],
            'batch_number' => $data['batch_number'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'product_type' => $data['product_type'] ?? null,
            'vaccination_group_id' => $data['vaccination_group'] ?? null,
            'pack_size' => $data['pack_size_unit'] ?? null,
            'pack_size_unit_type' => $data['pack_size_unit_type'] ?? null,
            'product_store_id' => $data['store_id'] ?? null,
            'rack_number' => $data['rack_number'] ?? null,
            'min_inventory_level' => $data['min_level'] ?? null,
            'max_inventory_level' => $data['max_level'] ?? null,
            'reorder_level_period' => $hasWarranty ? $warrantyPeriod : null,
            'reorder_level_date' => $hasWarranty ? Carbon::now()->addDays(5)->format('Y-m-d') : null,
            'mrp_price' => $data['mrp_price'] ?? null,
            'whole_sale_price' => $data['whole_sale_price'] ?? null,
            'full_less_price' => $data['full_less_price'] ?? null,
            'store_price' => $data['store_price'] ?? null,
            'trade_price' => $data['trade_price'] ?? null,
            'retail_price' => $data['retail_price'] ?? null,
            'purchase_price' => $data['purchase_price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'discount_amount' => $data['discount_amount'] ?? null,
            'tax_percentage' => $data['tax_percentage'] ?? null,
            'tax_amount' => $data['tax_amount'] ?? null,
            'discount_percentage' => $data['discount_percentage'] ?? null,
            'warranty_period' => $warrantyPeriod,
            'is_taxable' => (int) ($data['is_taxable'] ?? 0),
            'is_sale_on_tp' => (int) ($data['is_sale_on_tp'] ?? 0),
            'is_claimable' => (int) ($data['is_claimable'] ?? 0),
            'is_fridged' => (int) ($data['is_fridged'] ?? 0),
            'is_narcotic' => (int) ($data['is_narcotic'] ?? 0),
            'is_unwarranted' => (int) ($data['is_unwaranted'] ?? 0),
            'description' => $data['description'] ?? null,
        ];
    }
}
