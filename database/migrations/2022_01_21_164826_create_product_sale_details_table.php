<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSaleDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('product_sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_sale_id')->nullable()->constrained('product_sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->decimal('product_sale_price')->nullable();
            
            $table->integer('product_qty')->default(0);
            $table->integer('product_bonus_qty')->default(0);
            $table->integer('product_total_qty')->default(0);
            $table->decimal('product_discount')->nullable();
            $table->decimal('product_discount_percentage')->nullable();
            $table->decimal('product_total_price')->nullable();

            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_sale_details');
    }
}
