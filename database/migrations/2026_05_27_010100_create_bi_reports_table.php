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
        Schema::create('bi_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bi_section_id')->constrained('bi_sections')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('embed_url');
            $table->timestamps();

            $table->unique(['bi_section_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bi_reports');
    }
};

