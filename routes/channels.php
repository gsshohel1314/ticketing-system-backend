<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    if ($user->role === 'admin') {
        return true; // Admins can access all tickets
    }

    // Customers can only access their own tickets
    return Ticket::where('id', $ticketId)->where('user_id', $user->id)->exists();
});