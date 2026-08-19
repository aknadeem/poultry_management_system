<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPayablesTable extends Migration
{
    public function up()
    {
        Schema::create('account_payables', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date')->nullable();
            $table->decimal('dr', 15, 2)->default(0)->nullable();
            $table->decimal('cr', 15, 2)->default(0)->nullable();
            $table->decimal('balance', 15, 2)->default(0)->nullable();
            $table->decimal('total_amount', 15, 2)->default(0)->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0)->nullable();
            $table->decimal('remaining_amount', 15, 2)->default(0)->nullable();
            $table->text('narration')->nullable();
            $table->string('amount_status')->nullable();
            $table->string('amount_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('last_id')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_payables');
    }
}
