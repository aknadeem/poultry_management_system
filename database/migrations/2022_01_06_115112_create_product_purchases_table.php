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
            $table->unsignedBigInteger('purchase_number')->default(0);
            $table->string('purchase_code')->nullable();

            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->foreignId('party_company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->date('purchase_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('due_date_option')->nullable();
            $table->string('manual_number')->nullable();

            $table->decimal('total_amount')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->decimal('discount_percentage')->nullable();
            $table->decimal('other_charges')->nullable();
            $table->decimal('final_amount')->nullable();
            
            $table->tinyInteger('payment_status')->default(1);
            $table->integer('warranty_period')->nullable();
            
            $table->string('purchase_invoice')->nullable();
            $table->text('description')->nullable();

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
