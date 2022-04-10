<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSaleRebatesTable extends Migration
{
    public function up()
    {
        Schema::create('product_sale_rebates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rebate_item_id')->nullable()->constrained('product_sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('rebate_reason')->nullable();
            $table->integer('rebate_qty')->default(0);
            $table->text('rebate_description')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_sale_rebates');
    }
}
