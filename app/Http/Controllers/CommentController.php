<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function update(Request $request, Comment $comment)
    {
        // Pastikan hanya pemilik yang bisa edit
        abort_if($comment->user_id !== Auth::id(), 403);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $this->commentService->update($comment, $request->content);

        return back();
    }

    public function destroy(Comment $comment)
    {
        // Pastikan hanya pemilik yang bisa hapus
        abort_if($comment->user_id !== Auth::id(), 403);

        $this->commentService->destroy($comment);

        return back();
    }
}