<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Comment;
use App\Models\Composer;
use App\Models\Like;
use App\Models\Meter;
use App\Models\Origin;
use App\Models\Rhythm;
use App\Models\Tag;
use App\Models\Tunebook;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ArrangementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], only: ['edit', 'update', 'destroy', 'store']),
        ];
    }

    public function show(Arrangement $arrangement)
    {

        $user = Auth::user();
        $arrangement->load(['tune.composer', 'tune.origin', 'user:id,name', 'tags', 'meter', 'rhythm'])->loadCount(['likes']);
        $otherArrangements = $arrangement->tune->arrangements()
            ->where('id', '!=', $arrangement->id)
            ->with(['user:id,name'])
            ->withCount(['likes'])
            ->orderByDesc('likes_count')
            ->limit(5)
            ->get();

        $userLikeArrangement = $user
            ? $arrangement->likes()->where('user_id', $user->id)->exists()
            : false;

        $comments = Comment::where(function ($query) use ($arrangement) {
            $query->where('commentable_type', 'App\Models\Arrangement')
                ->where('commentable_id', $arrangement->id);
        })
            ->orWhere(function ($query) use ($arrangement) {
                $query->where('commentable_type', 'App\Models\Tune')
                    ->where('commentable_id', $arrangement->tune_id);
            })
            ->with([
                'commentable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Arrangement::class => ['tune'],
                    ]);
                }
            ])
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->paginate(10);

        $user_arrangement_for_this_tune = Arrangement::where('user_id', Auth::id())->where('tune_id', $arrangement->tune_id)->first();
        $tunebooks = $user ? $user->tunebooks()->get(['id', 'name'])->toArray() : [];

        return Inertia::render('Arrangements/Show', [
            'arrangement' => $arrangement,
            'other_arrangements' => $otherArrangements,
            'user_like_arrangement' => $userLikeArrangement,
            'comments' => $comments,
            'tunebooks' => $tunebooks,
            'suggestion' => request()->has('suggestion'),
            'user_arrangement_for_this_tune' => $user_arrangement_for_this_tune,
        ]);
    }

    public function edit(Arrangement $arrangement)
    {
        return Inertia::render('Arrangements/Edit', [
            'rhythms' => Rhythm::all()->sortBy('name')->values()->toArray(),
            'meters' => Meter::all(),
            'composers' => Composer::all()->sortBy('name')->values()->toArray(),
            'origins' => Origin::all()->sortBy('name')->values()->toArray(),
            'arrangement' => $arrangement->load(['tune.composer', 'tune.origin', 'meter', 'rhythm', 'tags']),
        ]);
    }

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $rhythmColumn = 'rhythm_name_' . (in_array($locale, ['fr', 'en', 'es', 'br']) ? $locale : 'en');
        $originColumn = 'origin_name_' . (in_array($locale, ['fr', 'en', 'es', 'br']) ? $locale : 'en');
        $comments = null;
        $tunebook = null;
        $query = Arrangement::search($request->input('search', ''));

        if (request('rhythm')) {
            $query->where($rhythmColumn, request('rhythm'));
        }
        if (request('composer')) {
            $query->where('composer_name', request('composer'));
        }
        if (request('origin')) {
            $query->where($originColumn, request('origin'));
        }

        if ($request->boolean('onlyUser')) {
            $query->where('user_id', auth()->id());
        }

        $filterIds = null;

        if ($request->boolean('userLike')) {
            $likesIds = Like::where('likeable_type', 'App\Models\Arrangement')
                ->where('user_id', auth()->id())
                ->pluck('likeable_id')->toArray();
            $filterIds = $likesIds;
        }

        if (request('tunebook')) {
            $tunebook = Tunebook::where('name', strtolower(request('tunebook')))->with('user:id,name')->firstOrFail();

            $tunebookIds = $tunebook->arrangements()->pluck('arrangements.id')->toArray();
            $filterIds = $filterIds !== null
                ? array_intersect($filterIds, $tunebookIds)
                : $tunebookIds;


            $comments = Comment::where('commentable_type', 'App\Models\Tunebook')
                ->with("user:id,name")
                ->where('commentable_id', $tunebook->id)
                ->orderByDesc('created_at')
                ->with([
                    'commentable' => function ($morphTo) {
                        $morphTo->morphWith([
                            Arrangement::class => ['tune'],
                        ]);
                    }
                ])
                ->paginate(10, ['*'], 'comments');
        }

        if ($filterIds !== null) {
            $query->whereIn('id', !empty($filterIds) ? $filterIds : [0]);
        }

        if ($request->boolean('best')) {
            $query->where('is_best_arrangement', true);
        }

        $query->options([
            'attributesToHighlight' => ['title', $originColumn, 'composer_name', $rhythmColumn, 'tags'],
            'attributesToSearchOn' => ['title', $originColumn, 'composer_name', $rhythmColumn, 'tags'],
            'highlightPreTag' => '<mark>',
            'highlightPostTag' => '</mark>',
            "limit" => 10
        ]);

        $query->orderByDesc('likes_count');
        $arrangementsPaginator = $query->paginateRaw(15);

        $rawMeilisearchResponse = $arrangementsPaginator->getCollection();
        $hits = $rawMeilisearchResponse->get('hits', []);

        $processedHits = collect($hits)->map(function ($arrangement) use ($originColumn, $rhythmColumn) {
            $arrangement = (array) $arrangement;

            $originNameValue = $arrangement[$originColumn] ?? null;
            $rhythmNameValue = $arrangement[$rhythmColumn] ?? null;

            $arrangement['origin_name'] = $originNameValue;
            $arrangement['rhythm_name'] = $rhythmNameValue;

            if (!isset($arrangement['_formatted']) || !is_array($arrangement['_formatted'])) {
                $arrangement['_formatted'] = [];
            }
            $arrangement['_formatted']['origin_name'] = $arrangement['_formatted'][$originColumn] ?? $originNameValue;
            $arrangement['_formatted']['rhythm_name'] = $arrangement['_formatted'][$rhythmColumn] ?? $rhythmNameValue;
            $columnsToUnset = [
                'origin_name_fr',
                'origin_name_en',
                'origin_name_es',
                'origin_name_br',
                'rhythm_name_fr',
                'rhythm_name_en',
                'rhythm_name_es',
                'rhythm_name_br',
            ];

            foreach ($columnsToUnset as $column) {
                unset($arrangement[$column]);
                unset($arrangement['_formatted'][$column]);
            }

            return $arrangement;
        })->all();

        $rawMeilisearchResponse->put('hits', $processedHits);
        $arrangementsPaginator->setCollection($rawMeilisearchResponse);


        $cacheDuration = 60;

        $cachedData = Cache::remember('tune_filter_data_' . $locale, $cacheDuration, function () {
            return [
                'composers' => Composer::all()->sortBy('name')->values()->toArray(),
                'rhythms' => Rhythm::all()->sortBy('name')->values()->toArray(),
                'origins' => Origin::all()->sortBy('name')->values()->toArray(),
                'meters' => Meter::all(),
            ];
        });

        return Inertia::render('Arrangements/Index', [
            'arrangements' => $arrangementsPaginator,
            'comments' => $comments,
            'tunebook' => $tunebook,
            'tunebook_likes' => $tunebook ? $tunebook->likes()->count() : 0,
            'filters' => $request->all(['search', 'rhythm', 'composer', 'origin', 'user', 'tunebook']) + [
                'best' => $request->boolean('best'),
                'onlyUser' => $request->boolean('onlyUser') && auth()->check(),
                'userLike' => $request->boolean('userLike') && auth()->check(),
            ],
            'rhythms' => $cachedData['rhythms'],
            'composers' => $cachedData['composers'],
            'origins' => $cachedData['origins'],
            'meters' => $cachedData['meters'],
        ]);
    }

    public function update(Arrangement $arrangement)
    {
        $validated = request()->validate([
            'meter_id' => 'nullable|exists:meters,id',
            'name' => 'nullable|string|max:255',
            'tempo' => 'nullable|integer',
            'parts' => 'nullable|string|max:255',
            'transcription' => 'nullable|string|max:255',
            'key' => 'string|max:255',
            'words' => 'nullable|string',
            'tune_body' => 'nullable|string',
            'rhythm_id' => 'nullable|exists:rhythms,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255'
        ]);

        if (isset($validated['tune_body'])) {
            $validated['tune_body_lines'] = json_encode(explode("\n", $validated['tune_body']));
        }

        if (isset($validated['words'])) {
            $validated['words_lines'] = json_encode(explode("\n", $validated['words']));
        }

        $arrangement->update($validated);

        if (array_key_exists('tags', $validated)) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => mb_strtolower($tagName)]);
                $tagIds[] = $tag->id;
            }
            $arrangement->tags()->sync($tagIds);
        } else {
            $arrangement->tags()->detach();
        }

        return redirect()->route('arrangements.show', [
            'arrangement' => $arrangement->id,
        ])->with('success', 'Arrangement updated successfully.');
    }

    public function destroy(Arrangement $arrangement)
    {
        if ($arrangement->tunebooks()->exists()) {
            $arrangement->user()->associate(1)->save();
        } else {
            $arrangement->delete();
        }
        return redirect()->route('dashboard');
    }
}
