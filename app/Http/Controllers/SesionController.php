<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Inscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SesionController extends Controller
{
    public function index()
    {
        $sesiones = Sesion::with(['curso', 'docente'])->withTrashed()->paginate(10);
        return view('sesiones.index', compact('sesiones'));
    }

    public function create()
    {
        $cursos = Curso::with('docentes.miembro')->get();
        $docentes = Docente::with('miembro')->where('activo', true)->get()->sortBy('miembro.nombre')->values();

        $docentesPorCurso = $cursos->mapWithKeys(fn ($c) => [
            $c->id => $c->docentes->pluck('id')->toArray()
        ]);

        return view('sesiones.create', compact('cursos', 'docentes', 'docentesPorCurso'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_curso' => 'required|exists:cursos,id',
            'id_docente' => 'nullable|exists:docentes,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'tema' => 'required|string|max:255',
            'observacion' => 'nullable|string',
        ]);

        $this->validarHorarioCurso($validated);

        Sesion::create($validated);
        return redirect()->route('sesiones.index')->with('success', 'Sesión creada con éxito.');
    }

    public function show($id)
    {
        $sesion = Sesion::with(['curso', 'docente.miembro'])->withTrashed()->findOrFail($id);

        $inscritos = Inscripcion::with('miembro')
            ->where('curso_id', $sesion->id_curso)
            ->whereNull('deleted_at')
            ->orderBy('fecha_inscripcion')
            ->get();

        $asistencias = DB::table('asistencias_sesion')
            ->where('sesion_id', $sesion->id)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('miembro_id');

        $sesionPasada = Carbon::parse($sesion->fecha)->lt(now()->startOfDay());

        $resumen = [
            'Presente' => 0,
            'Ausente' => 0,
            'Tardanza' => 0,
            'Justificado' => 0,
        ];

        foreach ($asistencias as $a) {
            if (isset($resumen[$a->estado])) {
                $resumen[$a->estado]++;
            }
        }

        return view('sesiones.show', compact('sesion', 'inscritos', 'asistencias', 'resumen', 'sesionPasada'));
    }

    public function edit($id)
    {
        $sesion = Sesion::withTrashed()->findOrFail($id);
        $cursos = Curso::with('docentes.miembro')->get();
        $docentes = Docente::with('miembro')->where('activo', true)->get()->sortBy('miembro.nombre')->values();

        $docentesPorCurso = $cursos->mapWithKeys(fn ($c) => [
            $c->id => $c->docentes->pluck('id')->toArray()
        ]);

        return view('sesiones.edit', compact('sesion', 'cursos', 'docentes', 'docentesPorCurso'));
    }

    public function update(Request $request, $id)
    {
        $sesion = Sesion::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'id_curso' => 'required|exists:cursos,id',
            'id_docente' => 'nullable|exists:docentes,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'tema' => 'required|string|max:255',
            'observacion' => 'nullable|string',
        ]);

        $this->validarHorarioCurso($validated, $sesion->id);

        $sesion->update($validated);
        return redirect()->route('sesiones.index')->with('success', 'Sesión actualizada con éxito.');
    }

    public function destroy($id)
    {
        $sesion = Sesion::findOrFail($id);
        $sesion->delete();
        return redirect()->route('sesiones.index')->with('success', 'Sesión eliminada lógicamente.');
    }

    public function restore($id)
    {
        $sesion = Sesion::withTrashed()->findOrFail($id);
        $sesion->restore();
        return redirect()->route('sesiones.index')->with('success', 'Sesión restaurada con éxito.');
    }

    public function generar(Curso $curso)
    {
        if (!$curso->f_fin) {
            return back()->with('error', 'El curso necesita fecha de fin para poder generar sesiones automáticamente.');
        }

        if (!$curso->tieneHorario()) {
            return back()->with('error', 'Configura primero los días y hora de inicio del curso.');
        }

        $existentes = Sesion::withTrashed()
            ->where('id_curso', $curso->id)
            ->get()
            ->map(fn ($s) => Carbon::parse($s->fecha)->toDateString() . ' ' . substr((string) $s->hora, 0, 5))
            ->all();

        $creadas = 0;
        $fecha = Carbon::parse($curso->f_inicio);
        $fin = Carbon::parse($curso->f_fin);
        $hora = substr((string) $curso->hora_inicio, 0, 5);

        while ($fecha->lte($fin)) {
            if (in_array($fecha->dayOfWeekIso, $curso->dias_semana)) {
                $clave = $fecha->toDateString() . ' ' . $hora;

                if (!in_array($clave, $existentes)) {
                    Sesion::create([
                        'id_curso' => $curso->id,
                        'fecha' => $fecha->toDateString(),
                        'hora' => $curso->hora_inicio,
                        'tema' => 'Sesión ' . ($creadas + 1),
                    ]);
                    $creadas++;
                }
            }
            $fecha->addDay();
        }

        if ($creadas === 0) {
            return back()->with('error', 'No se creó ninguna sesión nueva (ya existen todas las del horario asignado).');
        }

        return redirect()->route('sesiones.index')
            ->with('success', "Se generaron {$creadas} sesiones automáticamente para el curso {$curso->nombre}.");
    }

    private function validarHorarioCurso(array $datos, ?int $ignorarSesionId = null): void
    {
        $curso = Curso::find($datos['id_curso']);

        if (!$curso) {
            return;
        }

        $errores = [];
        $fecha = Carbon::parse($datos['fecha']);
        $horaInput = substr($datos['hora'], 0, 5);

        if ($fecha->lt(Carbon::parse($curso->f_inicio))) {
            $errores['fecha'] = 'La fecha es anterior al inicio del curso (' . Carbon::parse($curso->f_inicio)->format('d/m/Y') . ').';
        } elseif ($curso->f_fin && $fecha->gt(Carbon::parse($curso->f_fin))) {
            $errores['fecha'] = 'La fecha es posterior al fin del curso (' . Carbon::parse($curso->f_fin)->format('d/m/Y') . ').';
        }

        if (!empty($curso->dias_semana) && !in_array($fecha->dayOfWeekIso, $curso->dias_semana)) {
            $errores['fecha'] = 'Este curso se dicta los días ' . $curso->dias_nombres . '. Elige una fecha dentro del horario asignado.';
        }

        if ($curso->hora_inicio && $horaInput !== substr((string) $curso->hora_inicio, 0, 5)) {
            $errores['hora'] = 'Las sesiones de este curso son a las ' . substr((string) $curso->hora_inicio, 0, 5) . '.';
        }

        if (!$errores) {
            $queryDuplicada = Sesion::where('id_curso', $curso->id)->whereDate('fecha', $datos['fecha']);

            if ($ignorarSesionId) {
                $queryDuplicada->where('id', '!=', $ignorarSesionId);
            }

            if ($queryDuplicada->exists()) {
                $errores['fecha'] = 'Ya existe una sesión para este curso en esa fecha.';
            }
        }

        if ($errores) {
            throw ValidationException::withMessages($errores);
        }
    }
}
