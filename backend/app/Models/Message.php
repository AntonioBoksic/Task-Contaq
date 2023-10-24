<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'ticket_id',
        'user_id',
    ];

    // un messaggio può appartenere solo ad un ticket
    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }

    // un messaggio può essere scritto solo da uno user
    public function user(){
        return $this->belongsTo(User::class);
    }
}
