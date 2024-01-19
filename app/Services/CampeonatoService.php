<?php

namespace App\Services;

use App\Models\Resultado;
use App\Models\Time;
use App\Models\Jogo;

class CampeonatoService
{
    public function sortearQuartasDeFinal($campeonatoId, $timeIds)
    {
        // Verifica se já existem jogos das quartas de final
        $jaSorteado = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', 'quartas')
            ->exists();

        if ($jaSorteado) {
            return 'As quartas de final já foram sorteadas.';
        }

        if (count($timeIds) != 8) {
            return 'Número incorreto de times para as quartas de final.';
        }

        $timesEmbaralhados = collect($timeIds)->shuffle();
        for ($i = 0; $i < $timesEmbaralhados->count(); $i += 2) {
            Jogo::create([
                'campeonato_id' => $campeonatoId,
                'time_casa_id' => $timesEmbaralhados[$i],
                'time_visitante_id' => $timesEmbaralhados[$i + 1],
                'fase' => 'quartas',
                'data_jogo' => new \DateTime()
            ]);
        }

        return 'Quartas de final sorteadas com sucesso.';
    }

    public function sortearSemifinais($campeonatoId)
    {
        // Verifica se os jogos das quartas de final foram sorteados
        $quartasSorteadas = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', 'quartas')
            ->exists();

        if (!$quartasSorteadas) {
            return 'Os jogos das quartas de final ainda não foram sorteados.';
        }

        // Verifica se todos os jogos das quartas de final têm resultados
        $quartasConcluidas = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', 'quartas')
            ->doesntHave('resultado')
            ->exists();

        if ($quartasConcluidas) {
            return 'Os jogos das quartas de final ainda não foram concluídos.';
        }

        // Verifica se as semifinais já foram sorteadas
        $semifinaisJaSorteadas = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', 'semifinais')
            ->exists();

        if ($semifinaisJaSorteadas) {
            return 'As semifinais já foram sorteadas.';
        }

        $resultadosQuartas = $this->buscarResultadoFase($campeonatoId, 'quartas');
        $vencedoresQuartas = $resultadosQuartas['vencedores'];

        $vencedoresEmbaralhados = $vencedoresQuartas->shuffle();

        $semifinais = collect();

        for ($i = 0; $i < $vencedoresEmbaralhados->count(); $i += 2) {
            $jogo = Jogo::create([
                'campeonato_id' => $campeonatoId,
                'time_casa_id' => $vencedoresEmbaralhados[$i]->id,
                'time_visitante_id' => $vencedoresEmbaralhados[$i + 1]->id,
                'fase' => 'semifinais',
                'data_jogo' => new \DateTime()
            ]);
            $semifinais->push($jogo);
        }

        return $semifinais;
    }

    public function sortearFinais($campeonatoId)
    {
        // Verifica se as semifinais foram concluídas
        $semifinaisConcluidas = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', 'semifinais')
            ->doesntHave('resultado')
            ->exists();

        if ($semifinaisConcluidas) {
            return 'As semifinais ainda não foram concluídas.';
        }

        // Verifica se os jogos finais já foram sorteados
        $finaisJaSorteadas = Jogo::where('campeonato_id', $campeonatoId)
            ->whereIn('fase', ['final', 'terceiro_lugar'])
            ->exists();

        if ($finaisJaSorteadas) {
            return 'Os jogos finais já foram sorteados.';
        }

        $resultadosSemifinais = $this->buscarResultadoFase($campeonatoId, 'semifinais');

        $jogoFinal = Jogo::create([
            'campeonato_id' => $campeonatoId,
            'time_casa_id' => $resultadosSemifinais['vencedores'][0]->id,
            'time_visitante_id' => $resultadosSemifinais['vencedores'][1]->id,
            'fase' => 'final',
            'data_jogo' => new \DateTime()
        ]);

        $jogoTerceiroLugar = Jogo::create([
            'campeonato_id' => $campeonatoId,
            'time_casa_id' => $resultadosSemifinais['perdedores'][0]->id,
            'time_visitante_id' => $resultadosSemifinais['perdedores'][1]->id,
            'fase' => 'terceiro_lugar',
            'data_jogo' => new \DateTime()
        ]);

        return ['final' => $jogoFinal, 'terceiro_lugar' => $jogoTerceiroLugar];
    }

    public function registrarResultadoJogo($jogoId, $golsTimeCasa, $golsTimeVisitante)
    {
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

        return $resultado;
    }

    private function determinarVencedorEPerdedor($jogo, $golsTimeCasa, $golsTimeVisitante)
    {
        if ($golsTimeCasa > $golsTimeVisitante) {
            return (object)['vencedor' => $jogo->timeCasa, 'perdedor' => $jogo->timeVisitante];
        } elseif ($golsTimeVisitante > $golsTimeCasa) {
            return (object)['vencedor' => $jogo->timeVisitante, 'perdedor' => $jogo->timeCasa];
        } else {
            return $this->desempatarTimes($jogo->timeCasa, $jogo->timeVisitante);
        }
    }

    private function calcularPontuacao(Time $time)
    {
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

    private function desempatarTimes(Time $time1, Time $time2)
    {
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

    public function buscarResultadoFase($campeonatoId, $fase)
    {
        $jogos = Jogo::with('resultado.vencedor', 'resultado.perdedor')
            ->where('campeonato_id', $campeonatoId)
            ->where('fase', $fase)
            ->get();

        $vencedores = collect();
        $perdedores = collect();

        foreach ($jogos as $jogo) {
            if ($jogo->resultado && $jogo->resultado->vencedor) {
                $vencedores->push($jogo->resultado->vencedor);
            }
            if ($jogo->resultado && $jogo->resultado->perdedor) {
                $perdedores->push($jogo->resultado->perdedor);
            }
        }

        return ['vencedores' => $vencedores, 'perdedores' => $perdedores];
    }
}
