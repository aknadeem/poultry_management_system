<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalFarmChickHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('personal_farm_chick_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_farm_id')->nullable()->constrained('personal_farms')->onDelete('cascade');
            $table->foreignId('chick_purchase_id')->nullable()->constrained('chick_purchases')->onDelete('cascade');
            $table->integer('quantity');
            $table->date('folk_entry_date')->nullable();
            $table->date('folk_last_date')->nullable();
            $table->string('folk_period')->nullable();
            $table->date('chicken_sale_date')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personal_farm_chick_histories');
    }
}
