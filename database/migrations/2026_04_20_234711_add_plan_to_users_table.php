<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->after('remember_token')->constrained('plans')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('plan_id');
            }

            if (! Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('ru')->after('avatar');
            }

            if (! Schema::hasColumn('users', 'credits_balance')) {
                $table->bigInteger('credits_balance')->default(0)->after('locale');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'plan_id')) {
                $table->dropForeign(['plan_id']);
                $table->dropColumn('plan_id');
            }

            foreach (['avatar', 'locale', 'credits_balance'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
