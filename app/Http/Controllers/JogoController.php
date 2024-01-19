<?php

namespace App\Http\Controllers;

use App\Models\Jogo;
use App\Services\CampeonatoService;
use Illuminate\Http\Request;

class JogoController extends Controller
{
    protected $campeonatoService;

    public function __construct(CampeonatoService $campeonatoService)
    {
        $this->campeonatoService = $campeonatoService;
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
        $resultado = $this->campeonatoService->registrarResultadoJogo($jogoId, $request->gols_time_casa, $request->gols_time_visitante);

        // Verifica se o resultado foi registrado com sucesso
        if ($resultado) {
            return response()->json([
                'success' => true,
                'message' => 'Resultado do jogo registrado com sucesso.',
                'data' => $resultado
            ], 200);
        } else {
            // Em caso de falha, retorne uma mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível registrar o resultado do jogo.'
            ], 500);
        }
    }
}

