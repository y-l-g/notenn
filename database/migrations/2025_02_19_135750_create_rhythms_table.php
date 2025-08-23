<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rhythms', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_br');
            $table->timestamps();
        });

        $rhythms = File::getRequire(__DIR__.'/data/rhythms.php');

        foreach ($rhythms as $rhythm) {
            DB::table('rhythms')->insert([
                'name_en' => $rhythm['name_en'],
                'name_fr' => $rhythm['name_fr'],
                'name_es' => $rhythm['name_es'],
                'name_br' => $rhythm['name_br'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rhythms');
    }
};
