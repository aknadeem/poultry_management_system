<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaccinationSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('vaccination_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_farm_id')->nullable()->constrained('party_farms')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->date('schedule_date')->nullable();
            $table->boolean('is_vaccinated')->default(0)->nullable();
            $table->boolean('is_active')->default(1)->nullable();
            $table->date('vaccination_date')->nullable();
            $table->integer('vaccinated_chicks')->nullable();
            $table->longText('description')->nullable();
            $table->text('vaccinated_remarks')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vaccination_schedules');
    }
}
