<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('party_balances', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('party_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->string('financial_status')->default('open')->after('payment_status');
            $table->index(['reference_type', 'reference_id'], 'party_balances_reference_index');
        });

        Schema::table('broker_balances', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('broker_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->string('financial_status')->default('open')->after('status');
            $table->index(['reference_type', 'reference_id'], 'broker_balances_reference_index');
        });

        Schema::table('company_balances', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('model_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->string('financial_status')->default('open')->after('status');
            $table->index(['reference_type', 'reference_id'], 'company_balances_reference_index');
            $table->index(['type', 'model_id'], 'company_balances_type_model_index');
        });

        Schema::table('company_balance_payments', function (Blueprint $table) {
            $table->string('reference_no')->nullable()->after('description');
            $table->string('idempotency_key')->nullable()->unique()->after('reference_no');
            $table->string('payment_status')->default('posted')->after('idempotency_key');
            $table->foreignId('reversal_of_id')->nullable()->after('payment_status');
            $table->timestamp('reversed_at')->nullable()->after('reversal_of_id');
            $table->unsignedBigInteger('reversed_by')->nullable()->after('reversed_at');
            $table->text('reversal_reason')->nullable()->after('reversed_by');
        });

        Schema::table('party_balance_payments', function (Blueprint $table) {
            $table->string('idempotency_key')->nullable()->unique()->after('reference_no');
            $table->string('payment_status')->default('posted')->after('idempotency_key');
            $table->foreignId('reversal_of_id')->nullable()->after('payment_status');
            $table->timestamp('reversed_at')->nullable()->after('reversal_of_id');
            $table->unsignedBigInteger('reversed_by')->nullable()->after('reversed_at');
            $table->text('reversal_reason')->nullable()->after('reversed_by');
        });

        Schema::table('account_payables', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('model_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->unsignedBigInteger('company_balance_id')->nullable()->after('reference_id');
            $table->unsignedBigInteger('company_balance_payment_id')->nullable()->after('company_balance_id');
            $table->string('legacy_payment_row')->nullable()->after('company_balance_payment_id');
            $table->index(['amount_type', 'model_id'], 'account_payables_amount_type_model_index');
            $table->index(['reference_type', 'reference_id'], 'account_payables_reference_index');
        });
    }

    public function down(): void
    {
        Schema::table('party_balances', function (Blueprint $table) {
            $table->dropIndex('party_balances_reference_index');
            $table->dropColumn(['reference_type', 'reference_id', 'financial_status']);
        });

        Schema::table('broker_balances', function (Blueprint $table) {
            $table->dropIndex('broker_balances_reference_index');
            $table->dropColumn(['reference_type', 'reference_id', 'financial_status']);
        });

        Schema::table('company_balances', function (Blueprint $table) {
            $table->dropIndex('company_balances_reference_index');
            $table->dropIndex('company_balances_type_model_index');
            $table->dropColumn(['reference_type', 'reference_id', 'financial_status']);
        });

        Schema::table('company_balance_payments', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn([
                'reference_no',
                'idempotency_key',
                'payment_status',
                'reversal_of_id',
                'reversed_at',
                'reversed_by',
                'reversal_reason',
            ]);
        });

        Schema::table('party_balance_payments', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn([
                'idempotency_key',
                'payment_status',
                'reversal_of_id',
                'reversed_at',
                'reversed_by',
                'reversal_reason',
            ]);
        });

        Schema::table('account_payables', function (Blueprint $table) {
            $table->dropIndex('account_payables_amount_type_model_index');
            $table->dropIndex('account_payables_reference_index');
            $table->dropColumn([
                'reference_type',
                'reference_id',
                'company_balance_id',
                'company_balance_payment_id',
                'legacy_payment_row',
            ]);
        });
    }
};
