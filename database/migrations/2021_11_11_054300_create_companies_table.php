<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{

    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nic_name');
            $table->string('contact_no');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('cascade');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('company_logo')->nullable();
            $table->text('description')->nullable();
            $table->index(['country_id', 'province_id', 'city_id']);
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
