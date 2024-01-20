<?php

namespace App\Http\Controllers;

use App\Models\Jogo;
use App\Services\CampeonatoService;
use App\Services\JogoService;
use Illuminate\Http\Request;

class JogoController extends Controller
{
    protected $campeonatoService;
    protected $jogoService;

    public function __construct(CampeonatoService $campeonatoService, JogoService $jogoService)
    {
        $this->campeonatoService = $campeonatoService;
        $this->jogoService = $jogoService;
    }

    public function index()
    {
        $jogo = Jogo::all();
        return response()->json($jogo);
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
        $jogo = Jogo::with(['timeCasa', 'timeVisitante'])
            ->findOrFail($id);
        return response()->json($jogo);
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    public function registrarResultado(Request $request, $jogoId)
    {
        // Validação dos dados recebidos na requisição, se necessário
        $request->validate([
            'gols_time_casa' => 'required|integer',
            'gols_time_visitante' => 'required|integer',
        ]);

        // Chama o método do serviço para registrar o resultado do jogo
        $resultado = $this->jogoService->registrarResultadoJogo($jogoId, $request->gols_time_casa, $request->gols_time_visitante);

        // Verifica se o resultado foi registrado com sucesso
        if ($resultado) {
            return response()->json([
                'success' => true,
                'message' => 'Resultado do jogo registrado com sucesso.',
                'data' => $resultado
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível registrar o resultado do jogo.'
            ], 500);
        }
    }
}

