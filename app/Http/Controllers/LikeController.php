<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __invoke(Request $req)
    {
        $req->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer',
        ]);
        $likeable = $req->likeable_type::findOrFail($req->likeable_id);
        auth()->user()->like($likeable);

        return back();
    }
}
