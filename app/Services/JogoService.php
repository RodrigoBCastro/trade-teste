<?php

namespace App\Services;

use App\Models\Jogo;
use App\Models\Resultado;
use App\Models\Time;

class JogoService
{
    protected $campeonatoService;

    public function __construct(CampeonatoService $campeonatoService)
    {
        $this->campeonatoService = $campeonatoService;
    }

    public function registrarResultadoJogo($jogoId, $golsTimeCasa, $golsTimeVisitante) {
        $jogo = Jogo::find($jogoId);

        $jogo->update([
            'gols_time_casa' => $golsTimeCasa,
            'gols_time_visitante' => $golsTimeVisitante,
        ]);

        $resultado = $this->determinarVencedorEPerdedor($jogo, $golsTimeCasa, $golsTimeVisitante);

        $resultado = Resultado::updateOrCreate(
            ['jogo_id' => $jogoId],
            [
                'vencedor_id' => $resultado->vencedor->id,
                'perdedor_id' => $resultado->perdedor->id
            ]
        );

        $faseFinalizada = $this->campeonatoService->verificarFaseFinalizada($jogo->campeonato_id, $jogo->fase);

        if ($faseFinalizada) {
            switch ($jogo->fase) {
                case('quartas'):
                    $this->campeonatoService->sortearSemifinais($jogo->campeonato_id);
                    break;
                case('semifinais'):
                    $this->campeonatoService->sortearFinais($jogo->campeonato_id);
                    break;
                case('final'):
                case('terceiro_lugar'):
                    $this->campeonatoService->encerrarCampeonato($jogo->campeonato_id);
                    break;
                default:
                    break;
            }
        }

        return $resultado;
    }

    private function determinarVencedorEPerdedor($jogo, $golsTimeCasa, $golsTimeVisitante) {
        if ($golsTimeCasa > $golsTimeVisitante) {
            return (object)['vencedor' => $jogo->timeCasa, 'perdedor' => $jogo->timeVisitante];
        } elseif ($golsTimeVisitante > $golsTimeCasa) {
            return (object)['vencedor' => $jogo->timeVisitante, 'perdedor' => $jogo->timeCasa];
        } else {
            return $this->desempatarTimes($jogo->timeCasa, $jogo->timeVisitante);
        }
    }

    private function calcularPontuacao(Time $time) {
        $pontos = 0;

        foreach ($time->jogosCasa as $jogo) {
            $pontos += $jogo->gols_time_casa;
            $pontos -= $jogo->gols_time_visitante;
        }

        foreach ($time->jogosVisitante as $jogo) {
            $pontos += $jogo->gols_time_visitante;
            $pontos -= $jogo->gols_time_casa;
        }

        return $pontos;
    }

    private function desempatarTimes(Time $time1, Time $time2) {
        $pontuacaoTime1 = $this->calcularPontuacao($time1);
        $pontuacaoTime2 = $this->calcularPontuacao($time2);

        if ($pontuacaoTime1 > $pontuacaoTime2) {
            return (object)['vencedor' => $time1, 'perdedor' => $time2];
        } elseif ($pontuacaoTime2 > $pontuacaoTime1) {
            return (object)['vencedor' => $time2, 'perdedor' => $time1];
        } else {
            $vencedor = $time1->id < $time2->id ? $time1 : $time2;
            $perdedor = $time1->id < $time2->id ? $time2 : $time1;
            return (object)['vencedor' => $vencedor, 'perdedor' => $perdedor];
        }
    }
}
