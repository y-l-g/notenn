<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TagController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], only: ['edit', 'update', 'destroy', 'store']),
        ];
    }

    public function store(Request $request)
    {
        Tag::create($request->validate([
            'name' => 'required|string|max:30',
        ]));

        return redirect()->back();
    }
}
