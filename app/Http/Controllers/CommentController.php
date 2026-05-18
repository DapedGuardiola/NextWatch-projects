<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $movie)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie,
            'content' => $request->content,
            'reply_id' => null,
            'tagged_user_id' => null,
        ]);

        return back()->with('success', 'Comment added');
    }
}