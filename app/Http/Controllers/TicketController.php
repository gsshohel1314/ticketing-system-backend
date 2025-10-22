<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Traits\FileHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TicketResource;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

class TicketController extends Controller
{
    use FileHandler;

    public function index(): JsonResponse
    {
        try {
            $tickets = Ticket::with('comments')
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(5);

            return $this->successResponse(
                TicketResource::collection($tickets)->response()->getData(true),
                'Tickets fetched successfully.'
            );
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to fetch tickets.', 
                500
            );
        }
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {       
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $attachmentPath = null;

            // File upload using trait methods
            if ($request->hasFile('attachment')) {
                $attachmentPath = $this->uploadFile($request->file('attachment'), 'uploads/tickets');
            }

            $ticket = Ticket::create([
                'user_id' => Auth::id(),
                'subject' => $data['subject'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'attachment' => $attachmentPath,
            ]);

            DB::commit();

            return $this->successResponse($ticket, 'Ticket created successfully.');
        } catch (\Throwable $t) {
            DB::rollBack();
            
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to create ticket.',
                500
            );
        }
    }

    public function show(Ticket $ticket): JsonResponse
    {
        try {
            $ticket->load('comments');

            return $this->successResponse(
                new TicketResource($ticket),
                'Ticket fetched successfully.',
            );
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to fetch tickets.',
                500
            );
        }
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $attachmentPath = $ticket->attachment;

            // File upload & delete using trait methods
            if ($request->hasFile('attachment')) {
                $this->deleteFile($ticket->attachment);
                $attachmentPath = $this->uploadFile($request->file('attachment'), 'uploads/tickets');
            }

            $ticket->update([
                'subject'     => $data['subject'] ?? $ticket->subject,
                'description' => $data['description'] ?? $ticket->description,
                'category'    => $data['category'] ?? $ticket->category,
                'priority'    => $data['priority'] ?? $ticket->priority,
                'status'      => $data['status'] ?? $ticket->status,
                'attachment'  => $attachmentPath,
            ]);

            DB::commit();

            return $this->successResponse($ticket, 'Ticket updated successfully.');
        } catch (\Throwable $t) {
            DB::rollBack();

            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to update ticket.',
                500
            );
        }
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        try {
            // File delete using trait methods
            $this->deleteFile($ticket->attachment);

            // related comments and chats will be deleted automatically because onDelete('cascade') is set
            $ticket->delete();
            return $this->successResponse([], 'Ticket deleted successfully.');
        } catch (\Throwable $t) {
            return $this->errorResponse(
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                'Failed to delete ticket.',
                500
            );
        }
    }
}
