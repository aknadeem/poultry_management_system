<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvincesTable extends Migration
{
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            // $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('provinces');
    }
}
