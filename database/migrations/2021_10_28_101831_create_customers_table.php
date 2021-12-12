<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_type_id')->nullable()->constrained('customer_types')->onDelete('cascade');
            $table->string('name');
            $table->string('contact_no');
            $table->string('email')->nullable();
            $table->string('cnic')->unique();
            $table->string('cnic_image_front')->nullable();
            $table->string('cnic_image_back')->nullable();
            $table->string('customer_image')->nullable();
            $table->string('customer_signature')->nullable();
            $table->text('address')->nullable();
            
            $table->text('account_no')->nullable();
            $table->text('account_title')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('debit_limit')->nullable();
            $table->text('credit_limit')->nullable();
            $table->text('opening_balance')->nullable();
            $table->text('current_balance')->nullable();

            $table->string('farm_name')->nullable();
            $table->string('farm_image')->nullable();
            $table->string('farm_noc')->nullable();
            $table->string('farm_address')->nullable();

            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');

            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
