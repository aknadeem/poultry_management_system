<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalFarmsTable extends Migration
{
    public function up()
    {
        Schema::create('personal_farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_type_id')->constrained('farm_types')->onDelete('cascade');
            $table->foreignId('farm_subtype_id')->constrained('farm_subtypes')->onDelete('cascade');
            $table->string('farm_name');
            $table->string('farm_code')->nullable();
            $table->string('farm_noc')->nullable();
            $table->string('farm_image')->nullable();
            $table->text('farm_address')->nullable();
            $table->double('farm_area')->nullable();
            $table->double('farm_capacity')->nullable();
            $table->double('feed_room_size')->nullable();
            $table->boolean('is_active')->default(1);

            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('personal_farms');
    }
}
