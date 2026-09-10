<?php

namespace App\Http\Controllers;

use App\Models\Egreso;
use App\Models\Contrato;
use App\Models\Ingreso;
use App\Models\LiderIglesia;
use App\Http\Requests\EgresoRequest;
use Illuminate\Http\Request;

class EgresoController extends Controller
{
    public function saldoDisponible(?int $ignorarId = null): float
    {
        $totalIngresos = Ingreso::sum('monto_total');
        $totalEgresos = Egreso::sum('monto');

        if ($ignorarId) {
            $totalEgresos -= Egreso::withTrashed()->find($ignorarId)->monto ?? 0;
        }

        return $totalIngresos - $totalEgresos;
    }

    public function index()
    {
        $egresos = Egreso::with('contrato')->paginate(10);

        $totales = [
            'ingresos' => Ingreso::sum('monto_total'),
            'egresos' => Egreso::sum('monto'),
        ];
        $totales['balance'] = $totales['ingresos'] - $totales['egresos'];

        return view('egresos.index', compact('egresos', 'totales'));
    }

    public function create()
    {
        $contratos = Contrato::with('miembro')->get();
        $lideres = LiderIglesia::with('cargo')->get()->sortBy('nombre')->values();
        $saldoDisponible = $this->saldoDisponible();
        return view('egresos.create', compact('contratos', 'lideres', 'saldoDisponible'));
    }

    public function store(EgresoRequest $request)
    {
        $saldo = $this->saldoDisponible();

        if ($request->monto > $saldo) {
            return back()->withErrors(['monto' => 'No hay saldo suficiente. Saldo disponible: Bs ' . number_format($saldo, 2) . '.'])
                ->withInput();
        }

        Egreso::create($request->validated());
        return redirect()->route('egresos.index')->with('success', 'Egreso registrado con éxito.');
    }

    public function show($id)
    {
        $egreso = Egreso::with('contrato')->withTrashed()->findOrFail($id);
        return view('egresos.show', compact('egreso'));
    }

    public function edit($id)
    {
        $egreso = Egreso::withTrashed()->findOrFail($id);
        $contratos = Contrato::with('miembro')->get();
        $lideres = LiderIglesia::with('cargo')->get()->sortBy('nombre')->values();
        $saldoDisponible = $this->saldoDisponible($egreso->id);
        return view('egresos.edit', compact('egreso', 'contratos', 'lideres', 'saldoDisponible'));
    }

    public function update(EgresoRequest $request, $id)
    {
        $egreso = Egreso::withTrashed()->findOrFail($id);
        $saldo = $this->saldoDisponible($egreso->id);

        if ($request->monto > $saldo) {
            return back()->withErrors(['monto' => 'No hay saldo suficiente. Saldo disponible: Bs ' . number_format($saldo, 2) . '.'])
                ->withInput();
        }

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
