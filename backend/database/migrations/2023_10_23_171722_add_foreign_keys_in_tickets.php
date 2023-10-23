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
        Schema::table('tickets', function (Blueprint $table) {
            //qua creiamo la colonna per category_id
            $table->unsignedBigInteger("category_id");

            //qua creiamo la relazione con categories
            $table->foreign("category_id")
            ->references("id")
            ->on("categories");

            //qua creiamo la colonna per user_id (chi ha creato il ticket)
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
        Schema::table('tickets', function (Blueprint $table) {
            //prima eliminiamo la relazione di category_id
            $table->dropForeign("tickets_category_id_foreign");

            //poi droppiamo la colonna category_id
            $table->dropColumn("category_id");

            //prima eliminiamo la relazione di user_id
            $table->dropForeign("tickets_user_id_foreign");

            //poi droppiamo la colonna di user_id
            $table->dropColumn("user_id");
        });
    }
};
