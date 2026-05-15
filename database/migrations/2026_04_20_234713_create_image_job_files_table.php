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
        if (! Schema::hasTable('image_job_files')) {
            Schema::create('image_job_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('image_job_id')->constrained()->cascadeOnDelete();
                $table->string('original_name');                   // original filename
                $table->string('original_path');                   // storage path for source
                $table->string('result_path')->nullable();         // storage path for result
                $table->string('original_mime')->nullable();
                $table->unsignedBigInteger('original_size')->default(0);
                $table->unsignedBigInteger('result_size')->default(0);
                $table->unsignedInteger('original_width')->nullable();
                $table->unsignedInteger('original_height')->nullable();
                $table->unsignedInteger('result_width')->nullable();
                $table->unsignedInteger('result_height')->nullable();
                $table->string('status')->default('pending');      // pending,processing,done,failed,skipped
                $table->text('error_message')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['image_job_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_job_files');
    }
};
