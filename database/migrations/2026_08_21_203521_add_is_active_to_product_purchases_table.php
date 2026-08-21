<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_purchases') || Schema::hasColumn('product_purchases', 'is_active')) {
            return;
        }

        Schema::table('product_purchases', function (Blueprint $table) {
            $table->boolean('is_active')->default(1);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_purchases') || ! Schema::hasColumn('product_purchases', 'is_active')) {
            return;
        }

        Schema::table('product_purchases', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
