<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    protected $table = 'jogos';

    protected $fillable = [
        'campeonato_id',
        'time_casa_id',
        'time_visitante_id',
        'gols_time_casa',
        'gols_time_visitante',
        'data_jogo',
    ];

    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class, 'campeonato_id');
    }

    public function timeCasa()
    {
        return $this->belongsTo(Time::class, 'time_casa_id');
    }

    public function timeVisitante()
    {
        return $this->belongsTo(Time::class, 'time_visitante_id');
    }
}
