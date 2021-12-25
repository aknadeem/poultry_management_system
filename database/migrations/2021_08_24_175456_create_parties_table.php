<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartiesTable extends Migration
{
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_vendor')->nullable();
            $table->boolean('is_customer')->nullable();
            $table->string('name');
            $table->string('guardian_name');
            $table->string('cnic_no')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no');
            $table->string('business_no')->nullable();
            $table->string('manual_number')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('customer_type_id')->nullable();
            $table->unsignedBigInteger('vendor_type_id')->nullable();
            $table->unsignedBigInteger('customer_division_id')->nullable();
            $table->unsignedBigInteger('vendor_division_id')->nullable();

            $table->string('profile_picture')->nullable();
            $table->string('cnic_front')->nullable();
            $table->string('cnic_back')->nullable();
            $table->string('signature')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            
            $table->foreignId('contact_person_id')->nullable()->constrained('conduct_people')->onDelete('cascade');
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parties');
    }
}
