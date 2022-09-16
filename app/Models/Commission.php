<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commande_id',
        'taux',
        'montant_commission',
    ];

    protected function user(){
        return $this->belongsTo('App\Models\User');
    }
}
