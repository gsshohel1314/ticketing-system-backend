<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'ticket_id' => $this->ticket_id,
            'sender'    => [
                'id'    => $this->sender->id,
                'name'  => $this->sender->name,
            ],
            'message'   => $this->message,
            'is_read'   => (bool) $this->is_read,
            // 'created_at'=> $this->created_at->format('Y-m-d H:i:s'),
            'created_at'  => $this->created_at->diffForHumans(),
        ];
    }
}
