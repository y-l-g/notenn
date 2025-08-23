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
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->enum('name', [
                '2/4',
                '3/4',
                '4/4',
                '5/4',
                '6/4',
                '7/4',
                '5/8',
                '6/8',
                '7/8',
                '9/8',
                '10/8',
                '11/8',
                '12/8',
            ]);
            $table->timestamps();
        });

        $meters = File::getRequire(__DIR__.'/data/meters.php');

        foreach ($meters as $meter) {
            DB::table('meters')->insert([
                'name' => $meter,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
