<?php

namespace App\Http\Controllers;

use App\Models\Egreso;
use App\Models\LiderIglesia;
use App\Models\Contrato;
use App\Http\Requests\EgresoRequest;
use Illuminate\Http\Request;

class EgresoController extends Controller
{
    public function index()
    {
        $egresos = Egreso::with(['liderIglesia', 'contrato'])->withTrashed()->paginate(10);
        return view('egresos.index', compact('egresos'));
    }

    public function create()
    {
        $lideres = LiderIglesia::all();
        $contratos = Contrato::with('miembro')->get();
        return view('egresos.create', compact('lideres', 'contratos'));
    }

    public function store(EgresoRequest $request)
    {
        Egreso::create($request->validated());
        return redirect()->route('egresos.index')->with('success', 'Egreso registrado con éxito.');
    }

    public function show($id)
    {
        $egreso = Egreso::with(['liderIglesia', 'contrato', 'movimientos'])->withTrashed()->findOrFail($id);
        return view('egresos.show', compact('egreso'));
    }

    public function edit($id)
    {
        $egreso = Egreso::withTrashed()->findOrFail($id);
        $lideres = LiderIglesia::all();
        $contratos = Contrato::with('miembro')->get();
        return view('egresos.edit', compact('egreso', 'lideres', 'contratos'));
    }

    public function update(EgresoRequest $request, $id)
    {
        $egreso = Egreso::withTrashed()->findOrFail($id);
        $egreso->update($request->validated());
        return redirect()->route('egresos.index')->with('success', 'Egreso actualizado con éxito.');
    }

    public function destroy($id)
    {
        $egreso = Egreso::findOrFail($id);
        $egreso->delete();
        return redirect()->route('egresos.index')->with('success', 'Egreso eliminado lógicamente.');
    }

    public function restore($id)
    {
        $egreso = Egreso::withTrashed()->findOrFail($id);
        $egreso->restore();
        return redirect()->route('egresos.index')->with('success', 'Egreso restaurado con éxito.');
    }
}
