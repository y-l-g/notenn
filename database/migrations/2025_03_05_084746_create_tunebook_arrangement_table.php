<?php

use App\Models\Arrangement;
use App\Models\Tunebook;
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
        Schema::create('tunebook_arrangement', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tunebook::class)->cascadeOnDelete();
            $table->foreignIdFor(Arrangement::class)->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunebook_arrangement');
    }
};
