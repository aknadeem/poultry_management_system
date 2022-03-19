<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suplier_number')->default(0);
            $table->string('suplier_code')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('farm_name')->nullable();
            $table->string('type')->nullable();
            $table->string('cnic')->nullable();
            $table->text('address')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
