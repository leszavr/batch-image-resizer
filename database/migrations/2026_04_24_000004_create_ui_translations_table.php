<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ui_translations')) {
            Schema::create('ui_translations', function (Blueprint $table) {
                $table->id();
                $table->string('group')->default('ui');
                $table->string('key');
                $table->string('locale', 10);
                $table->text('value')->nullable();
                $table->timestamps();

                $table->unique(['group', 'key', 'locale']);
                $table->index(['locale', 'group']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_translations');
    }
};

