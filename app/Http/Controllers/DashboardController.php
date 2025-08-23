<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Comment;
use App\Models\Composer;
use App\Models\Tune;
use App\Models\Tunebook;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified', 'admin.only']),
        ];
    }

    public function __invoke()
    {
        $comments = auth()
            ->user()
            ->comments()
            ->with([
                'commentable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Arrangement::class => ['tune'],
                    ]);
                }
            ])
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'comments');

        $otherComments = Comment::whereHasMorph(
            'commentable',
            [Arrangement::class, Tune::class, Tunebook::class],
            function ($query, $type) {
                $query->where('user_id', auth()->id());
            }
        )
            ->with([
                'commentable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Arrangement::class => ['tune'],
                    ]);
                }
            ])
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'otherComments');

        $arrangements = auth()->user()->arrangements()->with('tune')->orderByDesc('created_at')->paginate(5, ['*'], 'arrangements');
        $tunebooks = auth()->user()->tunebooks()->orderByDesc('created_at')->paginate(5, ['*'], 'tunebooks');

        return Inertia::render('Dashboard', [
            'comments' => $comments,
            'arrangements' => $arrangements,
            'tunebooks' => $tunebooks,
            'otherComments' => $otherComments,
        ]);
    }

}
