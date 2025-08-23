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
        Schema::create('origins', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_br');
            $table->enum('type', ['continent', 'country', 'region']);
            $table->foreignId('parent_id')->nullable()->constrained('origins');
            $table->timestamps();

            $table->index('type');
            $table->index('parent_id');
        });

        $origins = File::getRequire(__DIR__.'/data/origins.php');

        foreach ($origins as $continent) {
            $continentId = DB::table('origins')->insertGetId([
                'name_en' => $continent['name_en'],
                'name_fr' => $continent['name_fr'],
                'name_es' => $continent['name_es'],
                'name_br' => $continent['name_br'],
                'type' => $continent['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! empty($continent['children'])) {
                foreach ($continent['children'] as $country) {
                    $countryId = DB::table('origins')->insertGetId([
                        'name_en' => $country['name_en'],
                        'name_fr' => $country['name_fr'],
                        'name_es' => $country['name_es'],
                        'name_br' => $country['name_br'],
                        'type' => $country['type'],
                        'parent_id' => $continentId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (! empty($country['children'])) {
                        foreach ($country['children'] as $region) {
                            DB::table('origins')->insert([
                                'name_en' => $region['name_en'],
                                'name_fr' => $region['name_fr'],
                                'name_es' => $region['name_es'],
                                'name_br' => $region['name_br'],
                                'type' => $region['type'],
                                'parent_id' => $countryId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('origins');
    }
};
