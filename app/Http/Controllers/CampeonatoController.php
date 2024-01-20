<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Services\CampeonatoService;
use Illuminate\Http\Request;

class CampeonatoController extends Controller
{
    protected $campeonatoService;

    public function __construct(CampeonatoService $campeonatoService)
    {
        $this->campeonatoService = $campeonatoService;
    }

    public function index()
    {
        $campeonato = Campeonato::all();
        return response()->json($campeonato);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $data_inicio = now();
        $data_fim = now()->addDays(30);

        $validatedData['data_inicio'] = $data_inicio;

        $campeonato = Campeonato::create($validatedData);

        $this->campeonatoService->sortearQuartasDeFinal($campeonato->id, $request->times);

        return response()->json($campeonato, 201);
    }

    public function show($id)
    {
        $campeonato = Campeonato::with(['jogos.timeCasa', 'jogos.timeVisitante', 'jogos.resultado', 'jogos.resultado.vencedor'])
            ->findOrFail($id);

        return response()->json($campeonato);
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    public function verificarClassificacao(Request $request, $campeonatoId)
    {
        return $this->campeonatoService->verificarClassificacao($campeonatoId);
    }
}

