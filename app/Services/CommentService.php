<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function getCommentsByMovie(int|string $movieId)
    {
        return Comment::with(['user', 'replies.user', 'taggedUser'])
            ->where('movie_id', $movieId)
            ->whereNull('reply_id')
            ->oldest()
            ->get();
    }

    public function store(array $data): Comment
    {
        return Comment::create([
            'user_id'        => Auth::id(),
            'movie_id'       => $data['movie_id'],
            'reply_id'       => $data['reply_id']        ?? null,
            'tagged_user_id' => $data['tagged_user_id']  ?? null,
            'content'           => $data['content'],
        ]);
    }
}