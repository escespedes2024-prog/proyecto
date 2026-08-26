<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Miembro;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $asistencias = Asistencia::with(['miembro', 'sesion.curso', 'user'])
            ->when($request->filled('q'), fn ($q) => $q->whereHas('miembro', fn ($m) => $m->where('nombre', 'like', '%' . $request->q . '%')))
            ->when($request->filled('curso_id'), fn ($q) => $q->porCurso($request->curso_id))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('desde'), fn ($q) => $q->whereHas('sesion', fn ($s) => $s->where('fecha', '>=', $request->desde)))
            ->when($request->filled('hasta'), fn ($q) => $q->whereHas('sesion', fn ($s) => $s->where('fecha', '<=', $request->hasta)))
            ->orderByDesc('sesiones.fecha')
            ->paginate(15)
            ->withQueryString();

        $cursos = Curso::orderBy('nombre')->get();

        return view('asistencia.index', compact('asistencias', 'cursos'));
    }

    public function store(Request $request, Sesion $sesion)
    {
        $validated = $request->validate([
            'asistencia' => 'required|array',
            'asistencia.*.estado' => 'required|in:Presente,Ausente,Tardanza,Justificado',
            'asistencia.*.observacion' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $sesion) {
            foreach ($validated['asistencia'] as $miembroId => $datos) {
                $valores = [
                    'estado' => $datos['estado'],
                    'observacion' => $datos['observacion'] ?? null,
                    'user_id' => auth()->id(),
                ];

                Asistencia::updateOrCreate(
                    ['sesion_id' => $sesion->id, 'miembro_id' => $miembroId],
                    $valores
                );
            }
        });

        return redirect()->route('sesiones.show', $sesion->id)
            ->with('success', 'Asistencia guardada con éxito.');
    }

    public function marcarTodos(Request $request, Sesion $sesion)
    {
        $inscritos = Inscripcion::where('curso_id', $sesion->id_curso)
            ->whereNull('deleted_at')
            ->pluck('miembro_id');

        DB::transaction(function () use ($inscritos, $sesion) {
            foreach ($inscritos as $miembroId) {
                Asistencia::updateOrCreate(
                    ['sesion_id' => $sesion->id, 'miembro_id' => $miembroId],
                    ['estado' => 'Presente', 'user_id' => auth()->id()]
                );
            }
        });

        return redirect()->route('sesiones.show', $sesion->id)
            ->with('success', 'Todos los inscritos marcados como presentes.');
    }

    public function historial(int $miembroId)
    {
        $asistencias = Asistencia::with(['sesion.curso', 'miembro'])
            ->porMiembro($miembroId)
            ->orderByDesc('sesiones.fecha')
            ->get();

        $miembro = Miembro::findOrFail($miembroId);

        $resumenPorCurso = $asistencias->groupBy(fn ($a) => $a->sesion?->curso?->nombre ?? 'Sin curso')
            ->map(fn ($lista) => [
                'total' => $lista->count(),
                'presentes' => $lista->where('estado', 'Presente')->count(),
                'porcentaje' => $lista->count() > 0
                    ? round(($lista->where('estado', 'Presente')->count() / $lista->count()) * 100, 1)
                    : 0,
            ]);

        return view('asistencia.historial', compact('miembro', 'asistencias', 'resumenPorCurso'));
    }
}
