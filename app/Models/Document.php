<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'fichier',
        'dossier_racine_id'
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class,'dossier_racine_id');
    }
}
