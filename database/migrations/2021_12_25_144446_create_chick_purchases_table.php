<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChickPurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('chick_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_status')->nullable();
            $table->string('purchase_code')->nullable();
            $table->foreignId('chick_grade_id')->nullable()->constrained('chick_grades')->onDelete('cascade');
            $table->string('bill_no')->nullable();
            $table->date('purchase_date')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->decimal('weight')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('price')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->decimal('total_price')->nullable();
            $table->decimal('final_price')->nullable();

            $table->string('bilty_number')->nullable();
            $table->string('bilty_charges')->nullable();
            $table->string('purchase_for')->nullable();
            $table->foreignId('personal_farm_id')->nullable()->constrained('personal_farms')->onDelete('cascade');
            $table->foreignId('party_farm_id')->nullable()->constrained('party_farms')->onDelete('cascade');
            $table->string('sale_order_number')->nullable();
            $table->string('delivery_order_number')->nullable();

            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_contact')->nullable();
            $table->text('description')->nullable();
            $table->string('picture')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chick_purchases');
    }
}
