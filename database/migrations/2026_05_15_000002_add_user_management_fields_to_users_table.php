<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'blocked_until')) {
                $table->timestamp('blocked_until')->nullable()->after('is_blocked');
            }
            if (! Schema::hasColumn('users', 'block_reason')) {
                $table->string('block_reason')->nullable()->after('blocked_until');
            }
            if (! Schema::hasColumn('users', 'unlimited_access')) {
                $table->boolean('unlimited_access')->default(false)->after('block_reason');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['is_blocked', 'blocked_until', 'block_reason', 'unlimited_access'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};