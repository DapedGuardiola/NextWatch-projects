<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReport;
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

    public function toggle(Comment $comment)
    {
        $userId = Auth::id();
 
        $existing = CommentLike::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->first();
 
        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            CommentLike::create([
                'user_id'    => $userId,
                'comment_id' => $comment->id,
            ]);
            $liked = true;
        }
 
        $likeCount = $comment->likes()->count();
 
        // Jika request AJAX, kembalikan JSON
        if (request()->expectsJson()) {
            return response()->json([
                'liked'      => $liked,
                'like_count' => $likeCount,
            ]);
        }
 
        return back();
    }

    public function store_report(Request $request, Comment $comment)
    {
        $request->validate([
            'reason' => 'required|in:inappropriate,spam,hate_speech,other',
            'note'   => 'nullable|string|max:500',
        ]);
 
        $userId = Auth::id();
 
        // Cek apakah sudah pernah report
        $alreadyReported = CommentReport::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->exists();
 
        if ($alreadyReported) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu sudah melaporkan komentar ini.',
                ], 422);
            }
            return back()->with('error', 'Kamu sudah melaporkan komentar ini.');
        }
 
        CommentReport::create([
            'user_id'    => $userId,
            'comment_id' => $comment->id,
            'reason'     => $request->reason,
            'note'       => $request->note,
        ]);

        // Auto-delete jika report >= 10
        $reportCount = CommentReport::where('comment_id', $comment->id)->count();
        $deleted = false;

        if ($reportCount >= 10) {
            $comment->delete();
            $deleted = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim.',
                'deleted' => $deleted,
            ]);
        }
    }
}