<?php

namespace App\Http\Controllers;

use App\Models\MovimientoFinanciero;
use App\Models\Ingreso;
use App\Models\Egreso;
use Illuminate\Http\Request;

class MovimientoFinancieroController extends Controller
{
    public function index()
    {
        $movimientos = MovimientoFinanciero::with(['ingreso', 'egreso'])->withTrashed()->paginate(10);
        return view('movimientos.index', compact('movimientos'));
    }

    public function create()
    {
        $ingresos = Ingreso::all();
        $egresos = Egreso::all();
        return view('movimientos.create', compact('ingresos', 'egresos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ingreso' => 'nullable|exists:ingresos,id',
            'id_egreso' => 'nullable|exists:egresos,id',
            'tipo' => 'required|string|in:Ingreso,Egreso',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:255',
            'mes_ano' => 'required|string|max:7', // Formato YYYY-MM
        ]);

        MovimientoFinanciero::create($validated);
        return redirect()->route('movimientos.index')->with('success', 'Movimiento financiero registrado con éxito.');
    }

    public function show($id)
    {
        $movimiento = MovimientoFinanciero::with(['ingreso', 'egreso'])->withTrashed()->findOrFail($id);
        return view('movimientos.show', compact('movimiento'));
    }

    public function edit($id)
    {
        $movimiento = MovimientoFinanciero::withTrashed()->findOrFail($id);
        $ingresos = Ingreso::all();
        $egresos = Egreso::all();
        return view('movimientos.edit', compact('movimiento', 'ingresos', 'egresos'));
    }

    public function update(Request $request, $id)
    {
        $movimiento = MovimientoFinanciero::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'id_ingreso' => 'nullable|exists:ingresos,id',
            'id_egreso' => 'nullable|exists:egresos,id',
            'tipo' => 'required|string|in:Ingreso,Egreso',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:255',
            'mes_ano' => 'required|string|max:7',
        ]);

        $movimiento->update($validated);
        return redirect()->route('movimientos.index')->with('success', 'Movimiento financiero actualizado con éxito.');
    }

    public function destroy($id)
    {
        $movimiento = MovimientoFinanciero::findOrFail($id);
        $movimiento->delete();
        return redirect()->route('movimientos.index')->with('success', 'Movimiento financiero eliminado lógicamente.');
    }

    public function restore($id)
    {
        $movimiento = MovimientoFinanciero::withTrashed()->findOrFail($id);
        $movimiento->restore();
        return redirect()->route('movimientos.index')->with('success', 'Movimiento financiero restaurado con éxito.');
    }
}
