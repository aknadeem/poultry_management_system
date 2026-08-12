<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_levels') && ! Schema::hasTable('user_roles')) {
            Schema::rename('user_levels', 'user_roles');
        }

        if (
            Schema::hasTable('users')
            && Schema::hasColumn('users', 'user_level_id')
            && ! Schema::hasColumn('users', 'user_role_id')
        ) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['user_level_id']);
                });
            } catch (\Throwable $e) {
                // SQLite or unnamed FK
            }

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('user_level_id', 'user_role_id');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->foreign('user_role_id')->references('id')->on('user_roles')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('users')
            && Schema::hasColumn('users', 'user_role_id')
            && ! Schema::hasColumn('users', 'user_level_id')
        ) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['user_role_id']);
                });
            } catch (\Throwable $e) {
                // SQLite or unnamed FK
            }

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('user_role_id', 'user_level_id');
            });
        }

        if (Schema::hasTable('user_roles') && ! Schema::hasTable('user_levels')) {
            Schema::rename('user_roles', 'user_levels');
        }

        if (
            Schema::hasTable('users')
            && Schema::hasColumn('users', 'user_level_id')
            && Schema::hasTable('user_levels')
        ) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('user_level_id')->references('id')->on('user_levels')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // FK may already exist
            }
        }
    }
};
