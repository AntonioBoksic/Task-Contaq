<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ticket;
use App\Models\Category;


class TicketController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        if ($user->role === 'technician') {
            $tickets = Ticket::with('category')->get();
        } else {
            $tickets = $user->tickets()->with('category')->get();

        }
        // mi mando pure tutte le categorie in modo da poter far vedere all0'utente in fase di creazione ticket tutte le varie opzioni
        $categories = Category::all();
        
        return response()->json(['tickets' => $tickets, 'categories' => $categories]);    }

    public function show(Ticket $ticket) {
        $user = auth()->user();

        // Se l'utente è un operatore e non è l'autore del ticket, nega l'accesso
        if ($user->role === 'operator' && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Accesso non autorizzato'], 403); // 403 Forbidden
        }

        // carica i messaggi associati al ticket in ordine cronologico
        $ticket->load(['messages' => function($query) {
            $query->orderBy('created_at', 'asc');
        }]);  

        return response()->json(['ticket' => $ticket], 200);
    }

    public function store(Request $request) {

        $user = auth()->user();

        // Controlla se l'utente è un operatore
        if ($user->role !== 'operator') {
            return response()->json(['message' => 'Solo gli operatori possono creare nuovi ticket'], 403); // 403 Forbidden
        }

        $data = $request->validate([
            'title' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $ticket = new Ticket($data);
        $ticket->user_id = $request->user()->id;
        $ticket->save();

        // carica i dati della categoria per il ticket appena creato in modo che possa renderizzare subito token.category.name nel frontend e non solo al refresh dato che viene chiamato con la index e contiene questo dato
        //senza questo quando creo nuovo ticket nel frontend non posso visualizzarlo senza refreshare
        // n.b. il load e il with sono simili, load si usa su un istanza, with si usa in una query
        $ticket->load('category');

        return response()->json(['ticket' => $ticket, 'message' => 'Ticket creato con successo!'], 201);
    }

    public function update(Request $request, $id) {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket non trovato'], 404);
        }

        $user = auth()->user();

        // Controllo permessi
        if ($user->role === 'technician' && $request->has('status')) {
            // I tecnici possono cambiare lo stato
            $data = $request->validate([
                'status' => 'required|in:aperto,in lavorazione,chiuso'
            ]);
            $ticket->status = $data['status'];
        } elseif ($user->role === 'operator' && $user->id == $ticket->user_id) {
            // Gli operatori possono cambiare il titolo e la categoria dei propri ticket
            $data = $request->validate([
                'title' => 'required|string',
                'category_id' => 'required|integer|exists:categories,id'
            ]);
            
            $ticket->title = $data['title'];
            $ticket->category_id = $data['category_id'];
            
        } else {
            return response()->json(['message' => 'Non autorizzato'], 403);
        }

        $ticket->save();

         // Carica la relazione con la categoria prima di restituire il ticket.
         //stessa cosa che facciamo nella store, dove spiego perchè usiamo load e non with
        $ticket->load('category');

        return response()->json(['ticket' => $ticket, 'message' => 'Ticket aggiornato con successo!']);
    }

    public function destroy($id) {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket non trovato'], 404);
        }

        $user = auth()->user();

        // Controllo permessi
        if ($user->role === 'operator' && $user->id == $ticket->user_id) {
            $ticket->delete(); // questo effettuerà una hard delete, se mi avanza tempo facciamo soft delete
            return response()->json(['message' => 'Ticket eliminato con successo!']);
        } else {
            return response()->json(['message' => 'Non autorizzato'], 403);
        }
    }



}
