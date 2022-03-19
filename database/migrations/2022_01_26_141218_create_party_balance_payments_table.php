<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyBalancePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('party_balance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->foreignId('party_balance_id')->nullable()->constrained('party_balances')->onDelete('cascade');

            $table->decimal('paid_amount')->nullable();
            $table->date('paid_date')->nullable();

            $table->string('payment_option')->nullable();
            $table->tinyInteger('payment_type')->nullable(1);
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('cheque_picture')->nullable();
            $table->string('invoice_picture')->nullable();
            
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('party_balance_payments');
    }
}
