<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('party_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->string('account_title');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('branch_code')->nullable();
            $table->decimal('opening_balance')->nullable();
            $table->decimal('dr')->nullable();
            $table->decimal('cr')->nullable();
            $table->boolean('is_active')->default(1);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('party_accounts');
    }
}
