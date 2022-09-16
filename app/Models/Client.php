<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'adresse_kin',
        'numero_whatsapp',
        'email_client',
        'nom_client'
    ];

    public function commande(){
        return $this->hasMany('App\Models\Commande');
    }
}
