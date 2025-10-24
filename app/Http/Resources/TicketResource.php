<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'subject'     => $this->subject,
            'description' => $this->description,
            'category'    => $this->category,
            'priority'    => $this->priority,
            'status'      => $this->status,
            'attachment'  => $this->attachment ? asset($this->attachment): null,
            'user'        => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'email'=> $this->user->email,
            ],
            'comments'    => CommentResource::collection($this->whenLoaded('comments')),
            'chats'       => ChatResource::collection($this->whenLoaded('chats')),
            'created_at'  => $this->created_at->diffForHumans(),
            'created_at'  => $this->created_at->diffForHumans(),
            // 'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
            // 'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
