<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Tunebook;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArrangementTunebookController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], only: ['store', 'index', 'show', 'destroy']),
        ];
    }

    public function store(Request $request, Arrangement $arrangement)
    {
        $validated = $request->validate([
            'tunebook_id' => 'required|exists:tunebooks,id',
        ]);

        $tunebook = Tunebook::find($validated['tunebook_id']);

        $tunebook->arrangements()->attach($arrangement->id);

        return back();
    }

    public function destroy(Request $request, Arrangement $arrangement, Tunebook $tunebook)
    {
        if ($tunebook->user_id !== auth()->id()) {
            abort(403);
        }

        $tunebook->arrangements()->detach($arrangement->id);

        return back();
    }
}
