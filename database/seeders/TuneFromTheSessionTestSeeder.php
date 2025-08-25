<?php

namespace Database\Seeders;

use App\Models\Arrangement;
use App\Models\Meter;
use App\Models\Rhythm;
use App\Models\Tune;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TuneFromTheSessionTestSeeder extends Seeder
{
    protected function simplifyMode(string $mode): string
    {
        $baseNote = substr($mode, 0, 1);
        $modeType = substr($mode, 1);
        $modeConversions = [
            'minor' => [
                'C' => 'Eb',
                'D' => 'F',
                'E' => 'G',
                'F' => 'Ab',
                'G' => 'Bb',
                'A' => 'C',
                'B' => 'D',
            ],
            'dorian' => [
                'C' => 'Bb',
                'D' => 'C',
                'E' => 'D',
                'F' => 'Eb',
                'G' => 'F',
                'A' => 'G',
                'B' => 'A',
            ],
            'mixolydian' => [
                'C' => 'F',
                'D' => 'G',
                'E' => 'A',
                'F' => 'Bb',
                'G' => 'C',
                'A' => 'D',
                'B' => 'E',
            ],
        ];
        if ($modeType === 'major' || !isset($modeConversions[$modeType])) {
            return $baseNote;
        }

        return $modeConversions[$modeType][$baseNote] ?? $baseNote;
    }

    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Arrangement::truncate();
        Tune::truncate();
        $rhythms = Rhythm::all()->keyBy(function ($item) {
            return mb_strtolower(($item->name_en));
        });
        $meters = Meter::all()->keyBy('name');
        $jsonFile = database_path('seeders/data/test.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);
        foreach ($jsonData as $entry) {
            $tune = Tune::firstOrCreate(
                ['title' => mb_strtolower(($entry['name']))],
                [
                    'user_id' => 1,
                    'origin_id' => 29,
                ]
            );

            $rhythmId = $rhythms[mb_strtolower($entry['type'])]->id ?? null;
            $meterId = $meters[mb_strtolower($entry['meter'])]->id ?? null;
            Arrangement::insert([
                'tune_id' => $tune->id,
                'tune_body_lines' => json_encode(explode("\r\n", $entry['abc'])),
                'meter_id' => $meterId,
                'key' => $this->simplifyMode($entry['mode']),
                'rhythm_id' => $rhythmId,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'source' => 'thesession',
                'source_url' => "https://thesession.org/tunes/{$entry['tune_id']}#setting{$entry['setting_id']}",
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
