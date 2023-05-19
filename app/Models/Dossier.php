<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
    use HasFactory;
    protected $fillable =[
        'nom',
        'slug',
        'dossier_racine_id'
    ];

    public function dossierRacine()
    {
        return $this->belongsTo(Dossier::class,'dossier_racine_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

}
