<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Comment;
use App\Models\Composer;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified']),
        ];
    }

    public function __invoke()
    {
        $tuneModificationSuggestions = Comment::where('is_suggestion', true)
            ->where('status', 'pending')
            ->where('commentable_type', "App\Models\Tune")
            ->with('commentable')
            ->get();

        $composersWithoutTunes = Composer::doesntHave('tunes')->get();

        $arrangementsWithoutLikesAndWithoutUser = Arrangement::doesntHave('likes')
            ->with('tune')
            ->whereUserId(auth()->id())
            ->paginate();

        $tunePairs = Cache::get('dashboard_tune_pairs', []);
        $composerPairs = Cache::get('dashboard_composer_pairs', []);

        return Inertia::render('AdminDashboard', compact(
            'tuneModificationSuggestions',
            'composersWithoutTunes',
            'arrangementsWithoutLikesAndWithoutUser',
            'tunePairs',
            'composerPairs'
        ));
    }
}
