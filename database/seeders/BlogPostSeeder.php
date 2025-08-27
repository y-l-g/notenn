<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostSeeder extends Seeder
{
    public function run()
    {
        $faker = FakerFactory::create();
        $locales = ['fr', 'es', 'en', 'br'];

        DB::disableQueryLog();

        for ($i = 0; $i < 100; $i++) {
            $post = BlogPost::create([
                'slug' => $faker->unique()->slug(3),
                'is_published' => $faker->boolean(80),
                'published_at' => $faker->boolean(80) ? Carbon::now()->subDays(rand(0, 365)) : null,
            ]);

            foreach ($locales as $locale) {
                $faker = FakerFactory::create($locale);

                $post->translations()->create([
                    'locale' => $locale,
                    'title' => $faker->sentence(6),
                    'content' => implode("\n\n", $faker->paragraphs(5)),
                ]);
            }
        }

        DB::enableQueryLog();
    }
}
