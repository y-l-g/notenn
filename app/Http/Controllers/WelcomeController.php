<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Comment;
use App\Models\Tune;
use App\Models\Tunebook;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $latestArrangements = Arrangement::search()
            ->latest()
            ->take(5)->raw();

        $tunesMostLiked = Arrangement::search()
            ->orderByDesc('likes_count')
            ->take(5)->raw();

        $tunebooksMostLiked = Tunebook::search()
            ->orderByDesc('likes_count')
            ->take(5)->raw();

        $irishTunes = Arrangement::search()
            ->where('is_best_arrangement', true)
            ->where('country', 'ireland')
            ->orderByDesc('likes_count')
            ->take(5)->raw();

        $latestComments = Comment::orderByDesc('created_at')
            ->with([
                'commentable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Arrangement::class => ['tune'],
                    ]);
                },
                'user:id,name',
            ])
            ->limit(5)
            ->get();

        return Inertia::render('Welcome', [
            'latestArrangements' => $latestArrangements,
            'tunesMostLiked' => $tunesMostLiked,
            'tunebooksMostLiked' => $tunebooksMostLiked,
            'irishTunes' => $irishTunes,
            'latestComments' => $latestComments,
        ]);
    }
}
