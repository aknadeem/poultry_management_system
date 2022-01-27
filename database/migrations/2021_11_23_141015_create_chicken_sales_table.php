<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChickenSalesTable extends Migration
{
    public function up()
    {
        Schema::create('chicken_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_status')->nullable();
            $table->string('manual_number')->nullable();
            $table->string('sale_code')->nullable();
            $table->string('bill_no')->nullable();
            $table->date('sale_date')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->foreignId('personal_farm_id')->nullable()->constrained('personal_farms')->onDelete('cascade');
            
            $table->integer('quantity')->nullable();
            $table->double('first_weight')->nullable();
            $table->double('second_weight')->nullable();
            $table->double('net_weight')->nullable();
            $table->double('total_weight')->nullable();
            $table->decimal('per_kg_price')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->decimal('total_price')->nullable();

            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_contact')->nullable();
            $table->text('description')->nullable();
            $table->string('picture')->nullable();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->onDelete('cascade');
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chicken_sales');
    }
}
