<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Message;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        User::factory(10)->create();
        Category::factory(5)->create();

        // Creazione dei Ticket e associazione con User e Category
        Ticket::factory(5)->make()->each(function ($ticket) {
        $ticket->user_id = User::where('role', 'operator')->inRandomOrder()->first()->id;
        $ticket->category_id = Category::inRandomOrder()->first()->id;
        $ticket->save();
        });

        // Creazione dei Messaggi e associazione con User e Ticket
        Message::factory(50)->make()->each(function ($message) {
            $ticket = Ticket::all()->random();
            $technician = User::where('role', 'technician')->inRandomOrder()->first();
        
            $possibleSendersIds = [$ticket->user_id];
            if ($technician) {
                $possibleSendersIds[] = $technician->id;
            }
        
            $sender_id = $possibleSendersIds[array_rand($possibleSendersIds)];
            $message->user_id = $sender_id;
            $message->ticket_id = $ticket->id;
            $message->save();
        });
        
    }

}
