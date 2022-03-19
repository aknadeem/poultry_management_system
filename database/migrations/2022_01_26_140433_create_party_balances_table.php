<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('party_balances', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->date('transaction_date')->nullable();
            $table->decimal('total_amount');
            $table->decimal('paid_amount')->nullable();
            $table->decimal('remaining_amount')->nullable();
            $table->tinyInteger('amount_type')->nullable();
            $table->tinyInteger('payment_status')->default(1);
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
        Schema::dropIfExists('party_balances');
    }
}
