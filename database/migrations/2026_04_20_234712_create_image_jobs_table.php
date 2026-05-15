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
        if (! Schema::hasTable('image_jobs')) {
            Schema::create('image_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('session_id')->nullable();          // for guests
                $table->string('status')->default('pending');      // pending,processing,done,failed,expired
                $table->string('name')->nullable();                // user-facing job name
                // Processing options stored as pipeline steps
                $table->json('pipeline')->nullable();              // [{"step":"resize","params":{...}}, ...]
                // Output settings
                $table->string('output_format')->nullable();       // jpg, png, webp, avif, tiff, gif
                $table->unsignedTinyInteger('output_quality')->default(85);
                $table->string('rename_prefix')->nullable();
                $table->string('rename_suffix')->nullable();
                // Stats
                $table->unsignedInteger('total_files')->default(0);
                $table->unsignedInteger('processed_files')->default(0);
                $table->unsignedInteger('failed_files')->default(0);
                $table->string('result_archive_path')->nullable(); // zip download path
                $table->unsignedBigInteger('result_size_bytes')->default(0);
                // Timestamps
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('expires_at')->nullable();       // auto-cleanup
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index('session_id');
                $table->index('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_jobs');
    }
};
