<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use Illuminate\Support\Facades\Auth;

class ArrangementFork extends Controller
{
    public function __invoke(Arrangement $arrangement)
    {
        $newArrangement = $arrangement->replicate();
        $newArrangement->user_id = Auth::id();
        $newArrangement->save();

        return redirect()->route('arrangements.edit', [
            'tune' => $newArrangement->tune->id,
            'arrangement' => $newArrangement->id,
        ])->with('success', 'Arrangement forked successfully!');

    }
}
