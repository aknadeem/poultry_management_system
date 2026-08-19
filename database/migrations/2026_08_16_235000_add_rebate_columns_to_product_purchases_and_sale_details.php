<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumnIfMissing('product_sale_details', 'is_rebate', function (Blueprint $table) {
            $table->boolean('is_rebate')->nullable()->default(0);
        });
        $this->addColumnIfMissing('product_sale_details', 'rebate_qty', function (Blueprint $table) {
            $table->integer('rebate_qty')->nullable()->default(0);
        });

        $this->addColumnIfMissing('product_purchase_details', 'is_rebate', function (Blueprint $table) {
            $table->boolean('is_rebate')->nullable()->default(0);
        });
        $this->addColumnIfMissing('product_purchase_details', 'rebate_qty', function (Blueprint $table) {
            $table->integer('rebate_qty')->nullable()->default(0);
        });

        $this->addColumnIfMissing('product_purchases', 'is_rebate', function (Blueprint $table) {
            $table->boolean('is_rebate')->nullable()->default(0);
        });
        $this->addColumnIfMissing('product_purchases', 'rebate_amount', function (Blueprint $table) {
            $table->decimal('rebate_amount', 15, 2)->nullable()->default(0);
        });

        $this->addColumnIfMissing('product_sales', 'is_rebate', function (Blueprint $table) {
            $table->boolean('is_rebate')->nullable()->default(0);
        });
        $this->addColumnIfMissing('product_sales', 'rebate_amount', function (Blueprint $table) {
            $table->decimal('rebate_amount', 15, 2)->nullable()->default(0);
        });
    }

    public function down(): void
    {
        $this->dropColumnIfExists('product_sale_details', 'is_rebate');
        $this->dropColumnIfExists('product_sale_details', 'rebate_qty');
        $this->dropColumnIfExists('product_purchase_details', 'is_rebate');
        $this->dropColumnIfExists('product_purchase_details', 'rebate_qty');
        $this->dropColumnIfExists('product_purchases', 'is_rebate');
        $this->dropColumnIfExists('product_purchases', 'rebate_amount');
        $this->dropColumnIfExists('product_sales', 'is_rebate');
        $this->dropColumnIfExists('product_sales', 'rebate_amount');
    }

    private function addColumnIfMissing(string $table, string $column, callable $definition): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, $definition);
    }

    private function dropColumnIfExists(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropColumn($column);
        });
    }
};
