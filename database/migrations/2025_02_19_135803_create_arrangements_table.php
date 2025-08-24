<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arrangements', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->foreignId('tune_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->jsonb('tune_body_lines')->nullable();
            $table->foreignId('meter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tempo')->nullable();
            $table->string('parts')->nullable();
            $table->string('transcription')->nullable();
            $table->jsonb('notes_lines')->nullable();
            $table->enum('key', [
                'C',
                'C#',
                'D',
                'D#',
                'E',
                'F',
                'F#',
                'G',
                'G#',
                'A',
                'A#',
                'B',
                'Bb',
                'Eb',
                'Ab',
                'Db',
                'Gb',
            ])->nullable();
            $table->string('source')->nullable();
            $table->jsonb('source_url')->nullable();
            $table->jsonb('words_lines')->nullable();
            $table->foreignId('rhythm_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrangements');
    }
};
