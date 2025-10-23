<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
