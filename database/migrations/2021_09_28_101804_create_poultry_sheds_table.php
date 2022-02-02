<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoultryShedsTable extends Migration
{

    public function up()
    {
        Schema::create('poultry_sheds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('shed_type')->nullable();
            $table->double('area')->nullable();
            $table->text('address')->nullable();
            $table->string('shed_logo')->nullable();
            $table->string('shed_picture')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('poultry_sheds');
    }
}
