<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPartyBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('customer_party_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->string('balance_type')->nullable();
            $table->date('transaction_date')->nullable();
            $table->decimal('dr')->nullable();
            $table->decimal('cr')->nullable();
            $table->decimal('balance')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->decimal('paid_amount')->nullable();
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
        Schema::dropIfExists('customer_party_balances');
    }
}
