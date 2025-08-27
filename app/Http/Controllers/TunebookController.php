<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Tunebook;
use App\Traits\HandlesScoutPagination;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class TunebookController extends Controller implements HasMiddleware
{
    use HandlesScoutPagination;

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: ['auth', 'verified'], only: ['update', 'destroy', 'store']),
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tunebooks,name',
        ]);

        Tunebook::create([
            'name' => $validated['name'],
            'user_id' => auth()->id(),
        ]);

        return back();
    }

    public function show(Tunebook $tunebook)
    {
        $arrangements = $tunebook->arrangements()->with([
            'tune.composer',
            'user:id,name',
            'likes',
        ])->paginate(10);

        return Inertia::render('Tunebooks/Show', compact(['tunebook', 'arrangements']));
    }

    public function index(Request $request)
    {
        $query = Tunebook::search($request->input('search', ''));
        if ($request->boolean('onlyUser')) {
            $query->where('user_id', auth()->id());
        }
        if ($request->boolean('userLike')) {
            $likesIds = Like::where('likeable_type', 'App\Models\Tunebook')->where('user_id', auth()->id())->pluck('likeable_id');
            $query->whereIn('id', $likesIds);
        }
        $query->options([
            'attributesToHighlight' => ['name', 'tags', 'user_name'],
            'attributesToSearchOn' => ['name', 'tags', 'user_name'],
            'highlightPreTag' => '<mark>',
            'highlightPostTag' => '</mark>',
        ]);
        $query->orderByDesc('likes_count');
        $tunebooks = $query->paginateRaw(15);

        return Inertia::render('Tunebooks/Index', [
            'tunebooks' => $tunebooks,
            'search' => $request->input('search', ''),
            'onlyUser' => $request->boolean('onlyUser') && auth()->check(),
            'userLike' => $request->boolean('userLike') && auth()->check(),
        ]);
    }

    public function destroy(Tunebook $tunebook)
    {
        $tunebook->delete();

        return back();
    }
}
