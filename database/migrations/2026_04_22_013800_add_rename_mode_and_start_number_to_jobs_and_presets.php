<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('image_jobs')) {
            Schema::table('image_jobs', function (Blueprint $table) {
                if (! Schema::hasColumn('image_jobs', 'rename_mode')) {
                    $table->string('rename_mode')->default('original')->after('output_quality');
                }

                if (! Schema::hasColumn('image_jobs', 'rename_start_number')) {
                    $table->unsignedInteger('rename_start_number')->default(1)->after('rename_suffix');
                }
            });
        }

        if (Schema::hasTable('presets')) {
            Schema::table('presets', function (Blueprint $table) {
                if (! Schema::hasColumn('presets', 'rename_mode')) {
                    $table->string('rename_mode')->default('original')->after('output_quality');
                }

                if (! Schema::hasColumn('presets', 'rename_start_number')) {
                    $table->unsignedInteger('rename_start_number')->default(1)->after('rename_suffix');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('image_jobs')) {
            Schema::table('image_jobs', function (Blueprint $table) {
                foreach (['rename_mode', 'rename_start_number'] as $column) {
                    if (Schema::hasColumn('image_jobs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('presets')) {
            Schema::table('presets', function (Blueprint $table) {
                foreach (['rename_mode', 'rename_start_number'] as $column) {
                    if (Schema::hasColumn('presets', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
