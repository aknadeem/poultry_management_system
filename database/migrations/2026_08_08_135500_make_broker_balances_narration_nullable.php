<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('broker_balances') || ! Schema::hasColumn('broker_balances', 'narration')) {
            return;
        }

        Schema::table('broker_balances', function (Blueprint $table) {
            $table->text('narration')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally irreversible: existing upgraded rows may contain null,
        // so restoring NOT NULL cannot be done safely without data loss.
    }
};
