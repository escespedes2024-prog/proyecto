<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Asistencia;
use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Culto;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Egreso;
use App\Models\Ingreso;
use App\Models\Inscripcion;
use App\Models\LiderIglesia;
use App\Models\Miembro;
use App\Models\Ministerio;
use App\Models\PagoCurso;
use App\Models\Sesion;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        $stats = [
            'miembros' => Miembro::count(),
            'miembros_activos' => Miembro::where('estado', 'Activo')->count(),
            'ministerios' => Ministerio::count(),
            'lideres' => LiderIglesia::where('activo', true)->count(),
            'cargos' => Cargo::count(),
            'cultos' => Culto::count(),
            'cursos' => Curso::count(),
            'docentes' => Docente::where('activo', true)->count(),
            'inscripciones' => Inscripcion::count(),
            'contratos_activos' => Contrato::where('activo', true)->count(),
            'ingresos_mes' => Ingreso::whereBetween('fecha', [$inicioMes, $finMes])->sum('monto_total'),
            'egresos_mes' => Egreso::whereBetween('fecha', [$inicioMes, $finMes])->sum('monto'),
        ];

        $stats['ingresos_totales'] = Ingreso::sum('monto_total');
        $stats['egresos_totales'] = Egreso::sum('monto');
        $stats['balance'] = $stats['ingresos_totales'] - $stats['egresos_totales'];
        $stats['balance_mes'] = $stats['ingresos_mes'] - $stats['egresos_mes'];

        $proximasActividades = Actividad::where('fecha', '>=', $hoy)
            ->with('ministerio')
            ->orderBy('fecha')
            ->take(5)
            ->get();

        $proximasSesiones = Sesion::where('fecha', '>=', $hoy)
            ->with(['curso', 'docente.miembro'])
            ->orderBy('fecha')
            ->take(5)
            ->get();

        $asistenciaStats = [
            'presentes' => Asistencia::where('estado', 'Presente')->count(),
            'ausentes' => Asistencia::where('estado', 'Ausente')->count(),
            'tardanzas' => Asistencia::where('estado', 'Tardanza')->count(),
        ];

        $ingresosUltimos6 = Ingreso::select(DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(monto_total) as total"))
            ->where('fecha', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
            ->where('fecha', '<=', $finMes)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $egresosUltimos6 = Egreso::select(DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(monto) as total"))
            ->where('fecha', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
            ->where('fecha', '<=', $finMes)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $ingresosPorTipo = Ingreso::select('tipo', DB::raw('SUM(monto_total) as total'))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->groupBy('tipo')
            ->orderByDesc('total')
            ->pluck('total', 'tipo');

        $egresosPorTipo = Egreso::select('tipo_egreso', DB::raw('SUM(monto) as total'))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->groupBy('tipo_egreso')
            ->orderByDesc('total')
            ->pluck('total', 'tipo_egreso');

        return view('dashboard', compact(
            'stats',
            'proximasActividades',
            'proximasSesiones',
            'asistenciaStats',
            'ingresosUltimos6',
            'egresosUltimos6',
            'ingresosPorTipo',
            'egresosPorTipo'
        ));
    }
}
