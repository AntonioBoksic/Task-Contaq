<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;
use App\Models\Ticket;


class MessageController extends Controller
{
    // questa la uso per mostrare tutti i messaggi di un certo ticket
    public function index($ticketId) {
        $user = auth()->user();
    
        $ticket = Ticket::find($ticketId);
    
        // Controllo se il ticket esiste
        if(!$ticket) {
            return response()->json(['message' => 'Ticket non trovato.'], 404);
        }
    
        // Controllo per operatori: possono visualizzare solo i messaggi dei ticket che hanno creato
        if ($user->role === 'operator' && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Accesso negato.'], 403); // 403 Forbidden
        }
    
        $messages = $ticket->messages->sortBy('created_at');
        return response()->json(['messages' => $messages], 200);
    }
    

    public function store(Request $request, $ticketId) {
        $ticket = Ticket::findOrFail($ticketId);
    
        // Controlla se l'utente ha l'autorizzazione a scrivere sul ticket (sia tecnici che operatori)
        $user = auth()->user();
    
        if ($user->id !== $ticket->user_id && $user->role !== 'technician') {
            return response()->json(['message' => 'Non autorizzato'], 403); // 403 Forbidden
        }
    
        $data = $request->validate([
            'content' => 'required|string',
        ]);
    
        $message = new Message($data);
        $message->ticket_id = $ticketId;
        $message->user_id = $user->id;
        $message->save();
    
        return response()->json(['message' => 'Messaggio creato con successo!', 'data' => $message], 201);
    }
}
