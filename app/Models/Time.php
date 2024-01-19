<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table = 'times';

    protected $fillable = [
        'nome',
    ];

    public function jogosCasa()
    {
        return $this->hasMany(Jogo::class, 'time_casa_id');
    }

    public function jogosVisitante()
    {
        return $this->hasMany(Jogo::class, 'time_visitante_id');
    }
}

