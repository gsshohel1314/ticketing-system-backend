<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ChatResource;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'message'   => 'required|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $chat = Chat::create([
                'ticket_id' => $data['ticket_id'],
                'sender_id' => Auth::id(),
                'message'   => $data['message']
            ]);

            DB::commit();

            return $this->successResponse(
                new ChatResource($chat),
                'Message sent successfully.'
            );
        } catch (\Throwable $t) {
            DB::rollBack();

            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to send message.',
                500
            );
        }
    }

    public function update(Request $request, Chat $chat): JsonResponse
    {
        if ($chat->sender_id !== Auth::id()) {
            return $this->errorResponse([], "You don't have right permission to update this message.", 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            $chat->update([
                'message' => $data['message'],
            ]);

            return $this->successResponse(
                new ChatResource($chat),
                'Message updated successfully.'
            );
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to update message.',
                500
            );
        }
    }

    public function destroy(Chat $chat): JsonResponse
    {
        if ($chat->sender_id !== Auth::id()) {
            return $this->errorResponse([], "You don't have right permission to delete this message.", 403);
        }

        try {
            $chat->delete();

            return $this->successResponse([], 'Message deleted successfully.');
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to delete message.',
                500
            );
        }
    }
}
