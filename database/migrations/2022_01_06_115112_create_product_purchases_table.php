<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->date('purchase_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('bonus_quantity')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->decimal('purchase_price')->nullable();
            $table->decimal('total_price')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->decimal('discount_percentage')->nullable();
            $table->decimal('tax_amount')->nullable();
            $table->decimal('tax_percentage')->nullable();
            $table->decimal('final_price')->nullable();
            $table->boolean('is_active')->default(1);
            $table->integer('warranty_period')->nullable();
            $table->string('purchase_invoice')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('party_company_id')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('product_purchases');
    }
}
