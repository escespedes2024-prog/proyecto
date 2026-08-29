<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\Actividad;
use App\Models\Culto;
use App\Http\Requests\IngresoRequest;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    public function index()
    {
        $ingresos = Ingreso::with(['actividad', 'culto'])->paginate(10);

        $totales = [
            'ingresos' => Ingreso::sum('monto_total'),
            'egresos' => Egreso::sum('monto'),
        ];
        $totales['balance'] = $totales['ingresos'] - $totales['egresos'];

        return view('ingresos.index', compact('ingresos', 'totales'));
    }

    public function create()
    {
        $actividades = Actividad::all();
        $cultos = Culto::all();
        return view('ingresos.create', compact('actividades', 'cultos'));
    }

    public function store(IngresoRequest $request)
    {
        Ingreso::create($request->validated());
        return redirect()->route('ingresos.index')->with('success', 'Ingreso registrado con éxito.');
    }

    public function show($id)
    {
        $ingreso = Ingreso::with(['actividad', 'culto'])->withTrashed()->findOrFail($id);
        return view('ingresos.show', compact('ingreso'));
    }

    public function edit($id)
    {
        $ingreso = Ingreso::withTrashed()->findOrFail($id);
        $actividades = Actividad::all();
        $cultos = Culto::all();
        return view('ingresos.edit', compact('ingreso', 'actividades', 'cultos'));
    }

    public function update(IngresoRequest $request, $id)
    {
        $ingreso = Ingreso::withTrashed()->findOrFail($id);
        $ingreso->update($request->validated());
        return redirect()->route('ingresos.index')->with('success', 'Ingreso actualizado con éxito.');
    }

    public function destroy($id)
    {
        $ingreso = Ingreso::findOrFail($id);
        $ingreso->delete();
        return redirect()->route('ingresos.index')->with('success', 'Ingreso eliminado lógicamente.');
    }

    public function restore($id)
    {
        $ingreso = Ingreso::withTrashed()->findOrFail($id);
        $ingreso->restore();
        return redirect()->route('ingresos.index')->with('success', 'Ingreso restaurado con éxito.');
    }
}
