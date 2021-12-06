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
            $table->decimal('paid_amount');
            $table->decimal('balance')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_balance_payments');
    }
}
