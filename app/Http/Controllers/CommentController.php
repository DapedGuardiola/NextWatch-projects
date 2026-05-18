<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $movie)
    {
        $request->validate([
            'body' => 'required|string|max:1000'
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie,
            'body' => $request->body,
        ]);

        return back();
    }
}