<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Tag;
use App\Models\Tunebook;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->createTestUsers();

            User::factory(50)->create();

            Tag::factory()->count(30)->create();

            Tunebook::factory()->count(100)->hasComments(10, [
                'status' => "pending"
            ])->create();
            $this->call(BlogPostSeeder::class);
            $this->call(TuneFromFileSeeder::class);
            // $this->call(TuneFromTheSessionSeeder::class);
        });
    }

    private function createTestUsers(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test2 User',
            'email' => 'test2@example.com',
        ]);
    }
}
