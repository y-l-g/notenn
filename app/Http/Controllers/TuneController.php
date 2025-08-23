<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Composer;
use App\Models\Origin;
use App\Models\Tune;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TuneController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], only: ['edit', 'update', 'destroy', 'store']),
        ];
    }

    public function create()
    {
        return Inertia::render('Tunes/Create', [
            'composers' => Composer::all()->sortBy('name')->values()->toArray(),
            'origins' => Origin::all()->sortBy('name')->values()->toArray(),
        ]);
    }

    public function show(Tune $tune)
    {
        return redirect()->route('arrangements.show', [
            'arrangement' => $tune->bestArrangement,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:tunes,title',
            'composer_id' => 'nullable|exists:composers,id',
            'origin_id' => 'nullable|exists:origins,id',
        ]);

        $tune = Tune::create([
            'title' => $validatedData['title'],
            'composer_id' => $validatedData['composer_id'],
            'origin_id' => $validatedData['origin_id'],
            'user_id' => Auth::id(),
        ]);

        $arrangement = Arrangement::create([
            'tune_id' => $tune->id,
            'user_id' => Auth::id(),
        ]);

        auth()->user()->like($arrangement);

        return redirect()->route('arrangements.edit', ['arrangement' => $arrangement]);
    }
}
