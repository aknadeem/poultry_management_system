<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        // Schema::dropIfExists('products');
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_number')->default(0);
            $table->string('product_code')->unique()->nullable();
            $table->string('bar_code')->unique()->nullable();
            $table->tinyInteger('product_group')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_type')->nullable();
            $table->date('entry_date')->nullable();
            $table->date('purchase_date')->nullable();

            $table->foreignId('party_company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('cascade');

            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();

            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->onDelete('cascade');
            $table->foreignId('vaccination_group_id')->nullable()->constrained('vaccination_groups')->onDelete('cascade');

            $table->string('pack_size')->nullable();
            $table->string('pack_size_unit_type')->nullable();

            $table->foreignId('product_store_id')->nullable()->constrained('product_stores')->onDelete('cascade');

            $table->integer('rack_number')->nullable();
            $table->integer('min_inventory_level')->nullable();
            $table->integer('max_inventory_level')->nullable();

            $table->string('quantity')->nullable();
            $table->string('total_quantity')->nullable();
            $table->string('remaining_quantity')->nullable();
            $table->integer('reorder_level_period')->nullable();
            $table->date('reorder_level_date')->nullable();
            
            $table->decimal('mrp_price')->nullable();
            $table->decimal('whole_sale_price')->nullable();
            $table->decimal('full_less_price')->nullable();
            $table->decimal('store_price')->nullable();
            $table->decimal('retail_price')->nullable();
            $table->decimal('trade_price')->nullable();
            $table->decimal('purchase_price')->nullable();
            $table->decimal('sale_price')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->decimal('discount_percentage')->nullable();
            $table->decimal('tax_amount')->nullable();
            $table->decimal('tax_percentage')->nullable();

            $table->integer('warranty_period')->nullable();
            $table->boolean('is_taxable')->default(0)->nullable();
            $table->boolean('is_sale_on_tp')->default(0)->nullable();
            $table->boolean('is_claimable')->default(0)->nullable();
            $table->boolean('is_fridged')->default(0)->nullable();
            $table->boolean('is_narcotic')->default(0)->nullable();
            $table->boolean('is_unwarranted')->default(0)->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('product_picture')->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
