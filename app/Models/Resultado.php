<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $table = 'resultados';

    protected $fillable = [
        'jogo_id',
        'vencedor_id',
        'perdedor_id',
        'empate',
    ];

    public function jogo()
    {
        return $this->belongsTo(Jogo::class, 'jogo_id');
    }

    public function vencedor()
    {
        return $this->belongsTo(Time::class, 'vencedor_id');
    }

    public function perdedor()
    {
        return $this->belongsTo(Time::class, 'perdedor_id');
    }
}
