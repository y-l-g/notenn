<?php

namespace App\Http\Controllers;

use App\Models\Arrangement;
use App\Models\Comment;
use App\Models\Tune;
use App\Models\Tunebook;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CommentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified'], only: ['update', 'destroy', 'store']),
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'arrangement_id' => 'nullable|exists:arrangements,id|required_without:tunebook_id',
            'tunebook_id' => 'nullable|required_without:arrangement_id|exists:tunebooks,id',
            'is_suggestion' => 'required|boolean',
        ]);
        if (isset($validated['arrangement_id'])) {
            $arrangement = Arrangement::findOrFail($validated['arrangement_id']);
            $commentData = [
                'user_id' => auth()->id(),
                'content' => $validated['content'],
                'is_suggestion' => $validated['is_suggestion'],
                'commentable_id' => $validated['arrangement_id'],
                'commentable_type' => Arrangement::class,
                'status' => $validated['is_suggestion'] ? 'pending' : 'approved',
            ];
            if ($validated['is_suggestion'] && $validated['suggestion_type'] === 'tune') {
                $commentData['commentable_id'] = $arrangement->tune_id;
                $commentData['commentable_type'] = Tune::class;
            }
        }
        if (isset($validated['tunebook_id'])) {
            $arrangement = Tunebook::findOrFail($validated['tunebook_id']);
            $commentData = [
                'user_id' => auth()->id(),
                'content' => $validated['content'],
                'is_suggestion' => $validated['is_suggestion'],
                'commentable_id' => $validated['tunebook_id'],
                'commentable_type' => Tunebook::class,
                'status' => $validated['is_suggestion'] ? 'pending' : 'approved',
            ];
        }
        Comment::create($commentData);

        return redirect()->back()->with('success', 'Comment created successfully.');
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update(['content' => $validated['content']]);

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Suggestion approved.');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Suggestion rejected.');
    }
}
