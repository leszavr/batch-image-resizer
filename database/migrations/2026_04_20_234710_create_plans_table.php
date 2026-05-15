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
        if (! Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');                           // Free, Pro, Team
                $table->string('slug')->unique();                 // free, pro, team
                $table->text('description')->nullable();
                $table->unsignedInteger('price_month')->default(0);  // kopecks / cents
                $table->unsignedInteger('price_year')->default(0);
                $table->string('currency', 3)->default('RUB');
                $table->unsignedInteger('max_files_per_job')->default(10);
                $table->unsignedInteger('max_file_size_mb')->default(10);
                $table->unsignedInteger('daily_jobs_limit')->default(3);
                $table->unsignedBigInteger('monthly_credits')->default(0);  // AI credits
                $table->boolean('watermark')->default(true);
                $table->boolean('api_access')->default(false);
                $table->boolean('priority_queue')->default(false);
                $table->json('allowed_formats')->nullable();      // ['jpg','png','webp',...]
                $table->json('allowed_operations')->nullable();   // ['resize','rotate',...]
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
