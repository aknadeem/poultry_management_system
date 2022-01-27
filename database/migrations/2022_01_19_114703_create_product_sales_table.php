<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSalesTable extends Migration
{
    public function up()
    {
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_number')->default(0);
            $table->string('sale_code')->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('cascade');
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->foreignId('party_company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->date('sale_date')->nullable();
            $table->string('due_date_option')->nullable();
            $table->string('manual_number')->nullable();
            $table->string('sale_type')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->decimal('discount_percentage')->nullable();
            $table->decimal('other_charges')->nullable();
            $table->decimal('final_amount')->nullable();
            $table->string('invoice_picture')->nullable();
            $table->text('description')->nullable();
            
            $table->tinyInteger('payment_status')->default(1);
            $table->tinyInteger('sale_status')->default(1);
            $table->boolean('is_active')->default(1);

            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('product_sales');
    }
}
