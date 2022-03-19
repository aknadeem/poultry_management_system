<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConductPeopleTable extends Migration
{
    public function up()
    {
        Schema::create('conduct_people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_number')->default(0);
            $table->string('person_code')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('cnic_no', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('picture')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conduct_people');
    }
}
