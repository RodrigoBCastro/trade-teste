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

    public function sortearQuartas(Request $request, $campeonatoId)
    {
        $this->campeonatoService->sortearQuartasDeFinal($campeonatoId);
    }

    public function sortearSemi(Request $request, $campeonatoId)
    {
        $this->campeonatoService->sortearSemifinais($campeonatoId);
    }

    public function sortearFinais(Request $request, $campeonatoId)
    {
        $this->campeonatoService->sortearFinais($campeonatoId);
    }
}

