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
            $query = Ticket::with('comments')->orderBy('id', 'DESC');

            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            $tickets = $query->get();

            return $this->successResponse(
                'Tickets fetched successfully.',
                TicketResource::collection($tickets)->response()->getData(true)
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

            return $this->successResponse('Ticket created successfully.', $ticket);
        } catch (\Throwable $t) {
            DB::rollBack();
            
            return $this->errorResponse(
                'Failed to create ticket.',
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
                500
            );
        }
    }

    public function show(Ticket $ticket): JsonResponse
    {
        try {
            $ticket->load('comments', 'chats');

            return $this->successResponse(
                'Ticket fetched successfully.',
                new TicketResource($ticket),
            );
        } catch (\Throwable $t) {
            return $this->errorResponse(
                'Failed to fetch tickets.',
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
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

            return $this->successResponse('Ticket updated successfully.', $ticket);
        } catch (\Throwable $t) {
            DB::rollBack();

            return $this->errorResponse(
                'Failed to update ticket.',
                ['error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'],
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
