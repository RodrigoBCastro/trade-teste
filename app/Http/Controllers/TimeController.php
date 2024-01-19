<?php

namespace App\Http\Controllers;

use App\Models\Time;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    public function index()
    {
        $times = Time::all();
        return response()->json($times);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $time = Time::create($validatedData);

        return response()->json($time, 201);
    }

    public function show($id)
    {
        $time = Time::findOrFail($id);
        return response()->json($time);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $time = Time::findOrFail($id);
        $time->update($validatedData);

        return response()->json($time);
    }

    public function destroy($id)
    {
        $time = Time::findOrFail($id);
        $time->delete();

        return response()->json(null, 204);
    }
}

