<?php

namespace App\Http\Controllers;

use App\Models\PagoCurso;
use App\Models\Inscripcion;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoCursoController extends Controller
{
    public function create(Inscripcion $inscripcion)
    {
        abort_unless($inscripcion->curso->tiene_pago, 403, 'Este curso no tiene costo de inscripción.');

        if ($inscripcion->saldo_pendiente <= 0) {
            return redirect()->route('inscripciones.gestion', $inscripcion->curso_id)
                ->with('error', 'Esta inscripción ya está pagada por completo.');
        }

        $inscripcion->load(['miembro', 'curso']);

        return view('pagos.create', compact('inscripcion'));
    }

    public function store(Request $request, Inscripcion $inscripcion)
    {
        abort_unless($inscripcion->curso->tiene_pago, 403, 'Este curso no tiene costo de inscripción.');

        $validated = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01', 'max:' . $inscripcion->saldo_pendiente],
            'fecha_pago' => ['required', 'date'],
            'metodo_pago' => ['required', 'string', 'in:Efectivo,Transferencia,Tarjeta'],
            'comprobante' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $inscripcion) {
            $pago = $inscripcion->pagos()->create(
                $validated + ['registrado_por' => auth()->id()]
            );

            // El pago aparece automáticamente en el módulo de Ingresos
            $pago->ingreso()->create([
                'monto_total' => $pago->monto,
                'fecha' => $pago->fecha_pago,
                'tipo' => 'Inscripción Curso: ' . $inscripcion->curso->nombre,
                'metodo_pago' => $pago->metodo_pago,
            ]);
        });

        return redirect()->route('inscripciones.gestion', $inscripcion->curso_id)
            ->with('success', 'Pago registrado con éxito.');
    }

    public function destroy($id)
    {
        $pago = PagoCurso::findOrFail($id);
        $cursoId = $pago->inscripcion->curso_id;

        DB::transaction(function () use ($pago) {
            $pago->ingreso()->delete();
            $pago->delete();
        });

        return redirect()->route('inscripciones.gestion', $cursoId)
            ->with('success', 'Pago eliminado lógicamente.');
    }

    public function restore($id)
    {
        $pago = PagoCurso::withTrashed()->findOrFail($id);
        $cursoId = $pago->inscripcion->curso_id;

        DB::transaction(function () use ($pago) {
            $pago->restore();

            $ingreso = Ingreso::withTrashed()
                ->where('pago_curso_id', $pago->id)
                ->first();
            $ingreso?->restore();
        });

        return redirect()->route('inscripciones.gestion', $cursoId)
            ->with('success', 'Pago restaurado con éxito.');
    }
}
