<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBalancePaymentsTable extends Migration
{

    public function up()
    {
        Schema::create('company_balance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_balance_id')->nullable()->constrained('company_balances')->onDelete('cascade');
            $table->foreignId('party_company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->decimal('paid_amount')->nullable();
            $table->decimal('balance')->nullable();
            $table->string('payment_option')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('cheque_picture')->nullable();
            $table->string('invoice_picture')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_balance_payments');
    }
}
