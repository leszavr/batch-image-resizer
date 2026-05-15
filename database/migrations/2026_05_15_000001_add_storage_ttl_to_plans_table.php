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
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'storage_ttl_hours')) {
                $table->unsignedInteger('storage_ttl_hours')->default(24)
                    ->after('monthly_credits')
                    ->comment('File storage duration in hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'storage_ttl_hours')) {
                $table->dropColumn('storage_ttl_hours');
            }
        });
    }
};