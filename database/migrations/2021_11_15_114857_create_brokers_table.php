<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokersTable extends Migration
{
    public function up()
    {
        Schema::create('brokers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guardian_name');
            $table->string('cnic_no', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no', 20)->nullable();
            $table->decimal('opening_balance')->default(0);
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
        Schema::dropIfExists('brokers');
    }
}
