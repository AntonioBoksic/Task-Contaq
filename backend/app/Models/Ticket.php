<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'category_id',
        'user_id',
    ];

    // ho dovuto aggiungere questo perchè senno quando creo ticket nel frontend, 
    // dal backend non manda lo status del ticket al frontend, dato che lo imposta invece all'interno del database,
    // in altre parole il model che mando al frontend non ha status, perhè quando poi viene caricato in database è lì che gli si aggiunge lo status
    protected $attributes = [
        'status' => 'aperto',
    ];

    // un ticket può avere solo una categoria
    public function category(){
        return $this->belongsTo(Category::class);
    }

    // un ticket può appartenere a multipli messaggi
    public function messages() {
        return $this->hasMany(Message::class);
    }

    // un ticket può avere solo un user come creatore
    public function user(){
        return $this->belongsTo(User::class);
    }

}
