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
            $table->date('purchase_date')->nulalble();
            $table->date('expiry_date')->nulalble();
            $table->integer('quantity')->nulalble();
            $table->integer('bonus_quantity')->nulalble();
            $table->integer('total_quantity')->nulalble();
            $table->decimal('purchase_price')->nulalble();
            $table->decimal('total_price')->nulalble();
            $table->decimal('discount_amount')->nulalble();
            $table->decimal('discount_percentage')->nulalble();
            $table->decimal('tax_amount')->nulalble();
            $table->decimal('tax_percentage')->nulalble();
            $table->decimal('final_price')->nulalble();
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
