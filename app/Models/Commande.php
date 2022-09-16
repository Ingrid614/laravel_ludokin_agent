<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_commande',
        'user_id',
        'client_id',
        'adresse_kin',
        'montant',
        'taux',
        'cout',
        'statut_id'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client');
    }

    public function preuveTransaction(){
        return $this->hasMany('App\Models\PreuveTransaction');
    }

    public function statut(){
        return $this->belongsTo('App\Models\Statut');
    }
}
