<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Miembro;
use Illuminate\Http\Request;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::with('miembro')->paginate(10);
        return view('docentes.index', compact('docentes'));
    }

    public function create()
    {
        $miembros = Miembro::doesntHave('docente')->get();
        return view('docentes.create', compact('miembros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_miembro' => 'required|exists:miembros,id|unique:docentes,id_miembro',
            'especialidad' => 'required|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        Docente::create($validated);
        return redirect()->route('docentes.index')->with('success', 'Docente registrado con éxito.');
    }

    public function show($id)
    {
        $docente = Docente::with(['miembro', 'cursos'])->withTrashed()->findOrFail($id);
        return view('docentes.show', compact('docente'));
    }

    public function edit($id)
    {
        $docente = Docente::withTrashed()->findOrFail($id);
        $miembros = Miembro::all();
        return view('docentes.edit', compact('docente', 'miembros'));
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'id_miembro' => 'required|exists:miembros,id|unique:docentes,id_miembro,' . $docente->id,
            'especialidad' => 'required|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $docente->update($validated);
        return redirect()->route('docentes.index')->with('success', 'Docente actualizado con éxito.');
    }

    public function destroy($id)
    {
        $docente = Docente::findOrFail($id);
        $docente->delete();
        return redirect()->route('docentes.index')->with('success', 'Docente eliminado lógicamente.');
    }

    public function restore($id)
    {
        $docente = Docente::withTrashed()->findOrFail($id);
        $docente->restore();
        return redirect()->route('docentes.index')->with('success', 'Docente restaurado con éxito.');
    }
}
