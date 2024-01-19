<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campeonato extends Model
{
    protected $table = 'campeonatos';

    protected $fillable = [
        'nome',
        'data_inicio',
        'data_fim',
    ];

    public function jogos()
    {
        return $this->hasMany(Jogo::class, 'campeonato_id');
    }
}
