<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            //qua creiamo la colonna per ticket_id
            $table->unsignedBigInteger("ticket_id");

            //qua creiamo la relazione
            $table->foreign("ticket_id")
            ->references("id")
            ->on("tickets");

            //qua creiamo la colonna per user_id (chi ha scritto il messaggio, che può essere operatore o tecnico, non per forza chi ha creato il ticket)
            $table->unsignedBigInteger("user_id");

            //qua creiamo la relazione
            $table->foreign("user_id")
            ->references("id")
            ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            //prima eliminiamo la relazione di ticket_id
            $table->dropForeign("messages_ticket_id_foreign");

            //poi droppiamo la colonna ticket_id
            $table->dropColumn("ticket_id");

            //prima eliminiamo la relazione di user_id
            $table->dropForeign("messages_user_id_foreign");

            //poi droppiamo la colonna di user_id
            $table->dropColumn("user_id");
        });
    }
};
