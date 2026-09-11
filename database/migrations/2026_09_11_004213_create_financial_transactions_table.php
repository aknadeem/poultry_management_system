<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->string('transaction_type');
            $table->string('direction');
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date')->nullable();
            $table->string('status')->default('posted');
            $table->foreignId('reversal_of_id')->nullable()->constrained('financial_transactions')->nullOnDelete();
            $table->string('idempotency_key')->nullable()->unique();
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reference_type', 'reference_id'], 'financial_transactions_reference_index');
            $table->index(['transaction_type', 'status'], 'financial_transactions_type_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
