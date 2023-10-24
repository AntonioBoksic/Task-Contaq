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
