<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Miembro;
use App\Models\Inscripcion;
use App\Http\Requests\CursoRequest;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index()
    {
        $cursos = Curso::withTrashed()->paginate(10);
        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('cursos.create');
    }

    public function store(CursoRequest $request)
    {
        $data = $request->validated();
        $data['dias_semana'] = $data['dias_semana'] ?? [];
        Curso::create($data);
        return redirect()->route('cursos.index')->with('success', 'Curso creado con éxito.');
    }

    public function show($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        $curso->load(['docentes.miembro', 'sesiones']);

        $inscripciones = Inscripcion::withTrashed()
            ->with(['miembro', 'pagos' => fn ($q) => $q->withTrashed()])
            ->withSum(['pagos' => fn ($q) => $q->withTrashed()], 'monto')
            ->where('curso_id', $id)
            ->orderBy('fecha_inscripcion')
            ->get();

        $miembrosDisponibles = Inscripcion::getMiembrosDisponibles($id);

        return view('cursos.show', compact('curso', 'inscripciones', 'miembrosDisponibles'));
    }

    public function edit($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        return view('cursos.edit', compact('curso'));
    }

    public function update(CursoRequest $request, $id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        $data = $request->validated();
        $data['dias_semana'] = $data['dias_semana'] ?? [];
        $curso->update($data);
        return redirect()->route('cursos.index')->with('success', 'Curso actualizado con éxito.');
    }

    public function destroy($id)
    {
        $curso = Curso::findOrFail($id);
        $curso->delete();
        return redirect()->route('cursos.index')->with('success', 'Curso eliminado lógicamente.');
    }

    public function restore($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        $curso->restore();
        return redirect()->route('cursos.index')->with('success', 'Curso restaurado con éxito.');
    }
}
