<?php

namespace App\Services;

use App\Models\Resultado;
use App\Models\Time;
use App\Models\Jogo;

class CampeonatoService
{
    public function sortearQuartasDeFinal($campeonatoId, $timeIds)
    {
        if ($this->verificaFaseSorteada($campeonatoId, 'quartas')) {
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
        if (!$this->verificaFaseSorteada($campeonatoId, 'quartas')) {
            return 'Os jogos das quartas de final ainda não foram sorteados.';
        }

        if (!$this->verificarFaseFinalizada($campeonatoId, 'quartas')) {
            return 'Os jogos das quartas de final ainda não foram concluídos.';
        }

        if ($this->verificaFaseSorteada($campeonatoId, 'semifinais')) {
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
        if (!$this->verificarFaseFinalizada($campeonatoId, 'semifinais')) {
            return 'As semifinais ainda não foram concluídas.';
        }

        if ($this->verificaFaseSorteada($campeonatoId,  ['final', 'terceiro_lugar'])) {
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

    public function verificaFaseSorteada($campeonatoId, $fase) {
        $faseSorteada = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', $fase)
            ->exists();

        if (!$faseSorteada) {
            return false;
        }

        return true;
    }

    public function verificarFaseFinalizada($campeonatoId, $fase) {
        $jogos = Jogo::where('campeonato_id', $campeonatoId)
            ->where('fase', $fase)
            ->doesntHave('resultado')
            ->exists();

        if ($jogos) {
            return false;
        }

        return true;
    }
}
