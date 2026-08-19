<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokerBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('broker_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->onDelete('cascade');
            $table->decimal('dr', 15, 2)->default(0)->nullable();
            $table->decimal('cr', 15, 2)->default(0)->nullable();
            $table->decimal('balance', 15, 2)->default(0)->nullable();
            $table->decimal('total_amount', 15, 2)->default(0)->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0)->nullable();
            $table->decimal('remaining_amount', 15, 2)->default(0)->nullable();
            $table->string('status')->default('unpaid');
            $table->boolean('is_active')->default(1);
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('broker_balances');
    }
}
