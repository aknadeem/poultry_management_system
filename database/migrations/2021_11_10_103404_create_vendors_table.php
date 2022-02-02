<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('nic_name')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('vendor_type_id')->nullable()->constrained('vendor_types')->onDelete('cascade');
            $table->string('contact_number')->nullable();
            $table->string('business_number')->nullable();
            $table->string('cnic_number')->unique();
            $table->string('cnic_image_front')->nullable();
            $table->string('cnic_image_back')->nullable();
            $table->decimal('credit_limit')->nullable();
            $table->decimal('debit_limit')->nullable();
            $table->decimal('opening_balance')->nullable();
            $table->string('business_type')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
