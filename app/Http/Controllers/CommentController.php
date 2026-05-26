<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(protected CommentService $commentService) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id'       => 'required',
            'content'           => 'required|string|max:1000',
            'reply_id'       => 'nullable|exists:comments,id',
            'tagged_user_id' => 'nullable|exists:users,id',
        ]);

        $this->commentService->store($validated);

        return back();
    }
}