<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyFarmsTable extends Migration
{
    public function up()
    {
        Schema::create('party_farms', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_personal')->default(0);
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->foreignId('farm_type_id')->constrained('farm_types')->onDelete('cascade');
            $table->foreignId('farm_subtype_id')->constrained('farm_subtypes')->onDelete('cascade');

            $table->string('farm_name');
            $table->string('farm_code')->nullable();
            $table->string('farm_noc')->nullable();
            $table->string('farm_image')->nullable();
            $table->text('farm_address')->nullable();
            $table->double('farm_area')->nullable();
            $table->double('feed_room_size')->nullable();
            $table->integer('farm_capacity')->nullable();
            $table->integer('folk_quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->boolean('is_occupied')->default(0);
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('party_farms');
    }
}
