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
            $table->decimal('dr')->nullable();
            $table->decimal('cr')->nullable();
            $table->decimal('balance')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->decimal('paid_amount')->nullable();
            $table->decimal('remaining_amount')->nullable();
            $table->string('status')->default('unpaid');
            $table->boolean('is_active')->default(1);
            $table->text('narration')->nullbale();
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
