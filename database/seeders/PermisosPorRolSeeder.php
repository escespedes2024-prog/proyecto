<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosPorRolSeeder extends Seeder
{
    /**
     * Módulos habilitados por rol. 'all' concede acceso total.
     */
    public const DEFINICIONES = [
        'Administrador' => 'all',
        'Secretario' => [
            'cargos', 'lideres', 'miembros', 'ministerios', 'actividades',
            'seguimiento', 'cursos', 'docentes', 'inscripciones', 'sesiones', 'asistencia',
        ],
        'Tesorero' => ['cultos', 'ingresos', 'egresos', 'contratos'],
    ];

    /**
     * Acciones que se evalúan para cada módulo.
     */
    public const ACCIONES = ['ver', 'crear', 'editar', 'eliminar', 'buscar'];

    /**
     * Aplica la matriz a todos los roles existentes.
     */
    public function run(): void
    {
        foreach (Role::withTrashed()->get() as $role) {
            $this->sembrarPara($role);
        }
    }

    /**
     * Crea o actualiza la fila de permisos de un rol para todos los módulos.
     */
    public function sembrarPara(Role $role): void
    {
        $modulos = self::modulos();
        $tipo = self::DEFINICIONES[$role->nombre] ?? [];
        $ahora = now();

        foreach ($modulos as $modulo => $accionesModulo) {
            $permitido = $tipo === 'all'
                || (is_array($tipo) && in_array($modulo, $tipo, true))
                || str_starts_with($modulo, 'reportes');

            $valores = [];
            foreach (self::ACCIONES as $accion) {
                $valores[$accion] = $permitido && in_array($accion, $accionesModulo, true);
            }

            DB::table('permisos')->updateOrInsert(
                ['role_id' => $role->id, 'modulo' => $modulo],
                $valores + ['updated_at' => $ahora, 'created_at' => $ahora]
            );
        }
    }

    /**
     * Módulos declarados en config/permisos.php con sus acciones válidas.
     *
     * @return array<string, array<int, string>>
     */
    public static function modulos(): array
    {
        $modulos = [];

        foreach (config('permisos.categorias', []) as $grupo) {
            foreach ($grupo as $modulo => $meta) {
                $modulos[$modulo] = $meta['acciones'] ?? [];
            }
        }

        return $modulos;
    }
}
