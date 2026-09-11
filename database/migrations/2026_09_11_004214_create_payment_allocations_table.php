<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type');
            $table->unsignedBigInteger('payment_id');
            $table->string('obligation_type');
            $table->unsignedBigInteger('obligation_id');
            $table->decimal('allocated_amount', 15, 2);
            $table->string('status')->default('posted');
            $table->foreignId('reversal_of_id')->nullable()->constrained('payment_allocations')->nullOnDelete();
            $table->string('idempotency_key')->nullable()->unique();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payment_type', 'payment_id'], 'payment_allocations_payment_index');
            $table->index(['obligation_type', 'obligation_id'], 'payment_allocations_obligation_index');
            $table->unique(
                ['payment_type', 'payment_id', 'obligation_type', 'obligation_id'],
                'payment_allocations_unique_pair'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
