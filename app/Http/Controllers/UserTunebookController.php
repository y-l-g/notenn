<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class UserTunebookController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], only: ['index']),
        ];
    }

    public function index()
    {
        return Inertia::render('Tunebooks/Index', [
            'tunebooks' => auth()
                ->user()
                ->tunebooks()
                ->with(['arrangements', 'user:id,name'])
                ->paginate(),
        ]);
    }
}
