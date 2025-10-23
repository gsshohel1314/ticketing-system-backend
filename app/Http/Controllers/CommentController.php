<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'comment'   => 'required|string|max:2000',
        ]);

        try {
            $comment = Comment::create([
                'user_id'   => Auth::id(),
                'ticket_id' => $data['ticket_id'],
                'comment'   => $data['comment'],
            ]);

            return $this->successResponse(
                new CommentResource($comment),
                'Comment added successfully.'
            );
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to add comment.',
                500
            );
        }
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        if ($comment->user_id !== Auth::id()) {
            return $this->errorResponse([], "You don't have right permission to update this comment.", 403);
        }

        $data = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        try {
            $comment->update([
                'comment' => $data['comment'],
            ]);

            return $this->successResponse(
                new CommentResource($comment),
                'Comment updated successfully.'
            );
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to update comment.',
                500
            );
        }
    }

    public function destroy(Comment $comment): JsonResponse
    {
        if ($comment->user_id !== Auth::id()) {
            return $this->errorResponse([], "You don't have right permission to delete this comment.", 403);
        }

        try {
            $comment->delete();

            return $this->successResponse([], 'Comment deleted successfully.');
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to delete comment.',
                500
            );
        }
    }
}
