<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use App\Models\Miembro;
use App\Models\LiderIglesia;
use App\Http\Requests\MinisterioRequest;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    public function index()
    {
        $ministerios = Ministerio::with('liderable')->withTrashed()->paginate(10);
        return view('ministerios.index', compact('ministerios'));
    }

    public function create()
    {
        $miembros = Miembro::all();
        $lideres = LiderIglesia::all();
        return view('ministerios.create', compact('miembros', 'lideres'));
    }

    public function store(MinisterioRequest $request)
    {
        Ministerio::create($request->validated());
        return redirect()->route('ministerios.index')->with('success', 'Ministerio creado con éxito.');
    }

    public function show($id)
    {
        $ministerio = Ministerio::with(['liderable', 'miembros'])->withTrashed()->findOrFail($id);
        return view('ministerios.show', compact('ministerio'));
    }

    public function edit($id)
    {
        $ministerio = Ministerio::withTrashed()->findOrFail($id);
        $miembros = Miembro::all();
        $lideres = LiderIglesia::all();
        return view('ministerios.edit', compact('ministerio', 'miembros', 'lideres'));
    }

    public function update(MinisterioRequest $request, $id)
    {
        $ministerio = Ministerio::withTrashed()->findOrFail($id);
        $ministerio->update($request->validated());
        return redirect()->route('ministerios.index')->with('success', 'Ministerio actualizado con éxito.');
    }

    public function destroy($id)
    {
        $ministerio = Ministerio::findOrFail($id);
        $ministerio->delete();
        return redirect()->route('ministerios.index')->with('success', 'Ministerio eliminado lógicamente.');
    }

    public function restore($id)
    {
        $ministerio = Ministerio::withTrashed()->findOrFail($id);
        $ministerio->restore();
        return redirect()->route('ministerios.index')->with('success', 'Ministerio restaurado con éxito.');
    }
}
