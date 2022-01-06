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
            $table->string('product_name');
            $table->string('product_code')->unique();
            $table->string('bar_code')->unique();

            $table->foreignId('party_company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('cascade');

            $table->string('batch_number')->nulalble();
            $table->string('serial_number')->nulalble();

            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->onDelete('cascade');
            $table->foreignId('vaccination_group_id')->nullable()->constrained('vaccination_groups')->onDelete('cascade');

            $table->string('pack_size')->nulalble();
            $table->string('pack_size_unit_type')->nulalble();

            $table->foreignId('product_store_id')->nullable()->constrained('product_stores')->onDelete('cascade');

            $table->integer('rack_number')->nulalble();
            $table->integer('min_inventory_level')->nulalble();
            $table->integer('max_inventory_level')->nulalble();

            $table->string('total_quantity')->nulalble();
            $table->string('remaining_quantity')->nulalble();
            $table->integer('reorder_level_period')->nulalble();
            $table->date('reorder_level_date')->nulalble();
            
            $table->decimal('mrp_price')->nulalble();
            $table->decimal('whole_sale_price')->nulalble();
            $table->decimal('full_less_price')->nulalble();
            $table->decimal('store_price')->nulalble();
            $table->decimal('retail_price')->nulalble();
            $table->decimal('trade_price')->nulalble();
            $table->decimal('purchase_price')->nulalble();
            $table->decimal('discount_amount')->nulalble();
            $table->decimal('discount_percentage')->nulalble();
            $table->decimal('tax_amount')->nulalble();
            $table->decimal('tax_percentage')->nulalble();

            $table->integer('warranty_period')->nulalble();
            $table->boolean('is_taxable')->default(0);
            $table->boolean('is_sale_on_tp')->default(0);
            $table->boolean('is_claimable')->default(0);
            $table->boolean('is_fridged')->default(0);
            $table->boolean('is_narcotic')->default(0);
            $table->boolean('is_unwarranted')->default(0);
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
