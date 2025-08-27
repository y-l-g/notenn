<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->withTranslation()
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'content' => $post->content,
                    'published_at' => $post?->published_at,
                ];
            });

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->published()
            ->withTranslation()
            ->firstOrFail();

        if (!$post->translation()) {
            abort(404);
        }

        return Inertia::render('Blog/Show', [
            'post' => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->title,
                'content' => $post->content,
                'published_at' => $post?->published_at,
            ],
        ]);
    }
}
