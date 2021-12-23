<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyBalanceLimitsTable extends Migration
{
    public function up()
    {
        Schema::create('party_balance_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('debit_limit');
            $table->decimal('credit_limit');
            $table->boolean('is_active')->default(1);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('party_balance_limits');
    }
}
