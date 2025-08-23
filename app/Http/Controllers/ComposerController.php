<?php

namespace App\Http\Controllers;

use App\Models\Composer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ComposerController extends Controller
{

    public function store(Request $request)
    {
        Composer::create($request->validate([
            'name' => 'required|string|max:255|unique:composers,name',
        ]));

        return back();
    }

    public function update(Request $request, Composer $composer)
    {
        $composer->update($request->validate([
            'name' => 'required|string|max:255|unique:composers,name,' . $composer->id,
        ]));

        return back();
    }
}
