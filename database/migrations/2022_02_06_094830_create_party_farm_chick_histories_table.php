<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyFarmChickHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('party_farm_chick_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_farm_id')->nullable()->constrained('party_farms')->onDelete('cascade');
            $table->foreignId('chick_purchase_id')->nullable()->constrained('chick_purchases')->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->date('entry_date')->nullable();
            $table->date('last_date')->nullable();
            $table->string('folk_period')->nullable();
            $table->integer('chick_age')->nullable();
            $table->date('chicken_sale_date')->nullable();
            $table->unsignedBigInteger('chicken_sale_id')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('party_farm_chick_histories');
    }
}
