<?php

namespace Database\Seeders;

use App\Likeable;
use App\Models\Arrangement;
use App\Models\Comment;
use App\Models\Composer;
use App\Models\Meter;
use App\Models\Origin;
use App\Models\Rhythm;
use App\Models\Tag;
use App\Models\Tune;
use App\Models\Tunebook;
use App\Models\User;
use App\Services\TuneParser;
use App\Taggable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TuneFromFileSeeder extends Seeder
{
    public function __construct(
        private readonly TuneParser $tuneParser
    ) {
    }

    protected function simplifyMode(string $mode): string
    {
        $baseNote = substr($mode, 0, 1);
        $modeType = substr($mode, 1);
        $modeConversions = [
            'm' => [
                'C' => 'Eb',
                'D' => 'F',
                'E' => 'G',
                'F' => 'Ab',
                'G' => 'Bb',
                'A' => 'C',
                'B' => 'D',
            ],
        ];
        if ($modeType === '' || !isset($modeConversions[$modeType])) {
            return $baseNote;
        }

        return $modeConversions[$modeType][$baseNote] ?? $baseNote;
    }

    public function run(): void
    {
        $users = User::all();
        $tunebooks = Tunebook::all();
        $tags = Tag::all();
        $origins = Origin::all();
        $rhythms = Rhythm::all();
        $meters = Meter::all();
        $status = ['pending', 'approved', 'rejected'];
        $filePath = database_path('seeders/data/tunes.txt');
        $fileContent = File::get($filePath);
        $tunesData = preg_split('/(?=X:)/', $fileContent);

        foreach ($tunesData as $tuneData) {
            $parsedData = $this->tuneParser->parse($tuneData);
            if (!$parsedData) {
                continue;
            }
            [$composer, $rhythm, $origin] = $this->createComposerAndRhythmAndOrigin($parsedData['info'], $origins, $rhythms);
            $tune = Tune::firstWhere('title', $parsedData['info']['T']);
            if ($tune) {
                continue;
            }
            $tune = Tune::Create(
                [
                    'title' => $parsedData['info']['T'],
                    'composer_id' => $composer?->id,
                    'origin_id' => $origin?->id,
                    'user_id' => $users->random()->id,
                ]
            );
            Comment::factory(rand(0, 2))->create([
                'commentable_id' => $tune->id,
                'commentable_type' => "App\Models\Tune",
                'is_suggestion' => true,
                'status' => $status[array_rand($status)],
            ]);
            Comment::factory(rand(0, 2))->create([
                'commentable_id' => $tune->id,
                'commentable_type' => "App\Models\Tune",
                'is_suggestion' => false,
                'status' => 'approved',
            ]);
            $this->createArrangements($tune, $parsedData, $users, $tunebooks, $tags, $rhythm, $meters, $status);
        }
        foreach ($tunebooks as $tunebook) {
            $this->createLikes($tunebook, $users);
            $this->attachTags($tunebook, $tags);
        }
    }

    private function createComposerAndRhythmAndOrigin(array $tuneInfo, $origins, $rhythms): array
    {
        $composer = isset($tuneInfo['C'])
            ? Composer::firstOrCreate(['name' => $tuneInfo['C']])
            : null;
        $rhythm = isset($tuneInfo['R'])
            ? $rhythms->firstWhere('name_en', ucwords($tuneInfo['R']))
            : null;
        if ($composer && rand(1, 100) <= 75) {
            $composer->origin_id = $origins->random()->id;
            $composer->save();
        }
        $origin = rand(1, 100) <= 75 ? $origins->random() : null;

        return [$composer, $rhythm, $origin];
    }

    private function createArrangements(Tune $tune, array $parsedData, $users, $tunebooks, $tags, $rhythm, $meters, $status): void
    {
        $arrangementCount = rand(2, 4);
        $meter = isset($parsedData['info']['M'])
            ? $meters->firstWhere('name', $parsedData['info']['M'])
            : null;
        $attributes = $this->getArrangementAttributes($parsedData, $meter, $users);
        $usersForArrangement = $users->random($arrangementCount)->pluck('id');
        for ($i = 0; $i < $arrangementCount; $i++) {
            $arrangement = Arrangement::create(array_merge($attributes, ['tune_id' => $tune->id], ['user_id' => $usersForArrangement[$i]]));
            Comment::factory(rand(0, 2))->create([
                'commentable_id' => $arrangement->id,
                'commentable_type' => "App\Models\Arrangement",
                'is_suggestion' => true,
                'status' => $status[array_rand($status)],
            ]);
            Comment::factory(rand(0, 2))->create([
                'commentable_id' => $arrangement->id,
                'commentable_type' => "App\Models\Arrangement",
                'is_suggestion' => false,
                'status' => 'approved',
            ]);
            $this->attachTunebooks($arrangement, $tunebooks);
            $this->createLikes($arrangement, $users);
            $this->attachTags($arrangement, $tags);
            if ($rhythm) {
                $arrangement->rhythm()->associate($rhythm)->save();
            }
        }
    }

    private function getArrangementAttributes(array $parsedData, $meter, $users): array
    {
        return [
            'tune_body_lines' => !empty($parsedData['body_lines']) ? json_encode($parsedData['body_lines']) : null,
            'meter_id' => $meter?->id,
            'tempo' => $parsedData['info']['Q'] ?? null,
            'parts' => $parsedData['info']['P'] ?? null,
            'transcription' => $parsedData['info']['Z'] ?? null,
            'notes_lines' => !empty($parsedData['note_lines']) ? json_encode($parsedData['note_lines']) : null,
            'key' => $this->simplifyMode($parsedData['info']['K']) ?? null,
            'words_lines' => !empty($parsedData['word_lines']) ? json_encode($parsedData['word_lines']) : null,
            'user_id' => $users->random()->id,
        ];
    }

    private function attachTunebooks(Arrangement $arrangement, $tunebooks): void
    {
        $arrangement->tunebooks()->attach(
            $tunebooks->random(rand(1, 5))->pluck('id')
        );
    }

    private function attachTags(Taggable $taggable, $tags): void
    {
        $taggable->tags()->attach(
            $tags->random(rand(0, 5))->pluck('id')
        );
    }

    private function createLikes(Likeable $likeable, $users): void
    {
        $likeable->likes()->createMany(
            $users->random(rand(0, 3))->map(fn($user) => ['user_id' => $user->id])
        );
    }
}
