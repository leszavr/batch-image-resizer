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
        if (! Schema::hasTable('presets')) {
            Schema::create('presets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->boolean('is_global')->default(false);      // system presets visible to all
                $table->json('pipeline');                          // same format as image_jobs.pipeline
                $table->string('output_format')->nullable();
                $table->unsignedTinyInteger('output_quality')->default(85);
                $table->string('rename_prefix')->nullable();
                $table->string('rename_suffix')->nullable();
                $table->unsignedInteger('used_count')->default(0);
                $table->timestamps();
                $table->index(['user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presets');
    }
};
