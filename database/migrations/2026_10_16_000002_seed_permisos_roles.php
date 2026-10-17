<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Inicializa los permisos de los roles existentes respetando el acceso
     * que tenían con el esquema anterior (basado en RoleMiddleware):
     *  - Administrador: acceso total.
     *  - Secretario: módulos operativos (parametrización + transaccional).
     *  - Tesorero: finanzas (cultos, ingresos, egresos, contratos).
     *  - Cualquier otro rol: solo reportes (abiertos a todos antes).
     */
    public function up(): void
    {
        $modulos = [];
        foreach (config('permisos.categorias', []) as $grupo) {
            foreach ($grupo as $modulo => $meta) {
                $modulos[$modulo] = $meta['acciones'] ?? [];
            }
        }

        $definiciones = [
            'Administrador' => 'all',
            'Secretario' => [
                'cargos', 'lideres', 'miembros', 'ministerios', 'actividades',
                'seguimiento', 'cursos', 'docentes', 'inscripciones', 'sesiones', 'asistencia',
            ],
            'Tesorero' => ['cultos', 'ingresos', 'egresos', 'contratos'],
        ];

        $todasLasAcciones = ['ver', 'crear', 'editar', 'eliminar', 'buscar'];
        $ahora = now();

        foreach (DB::table('roles')->get() as $role) {
            $tipo = $definiciones[$role->nombre] ?? [];

            foreach ($modulos as $modulo => $accionesModulo) {
                $permitido = $tipo === 'all'
                    || (is_array($tipo) && in_array($modulo, $tipo, true))
                    || str_starts_with($modulo, 'reportes');

                $valores = [];
                foreach ($todasLasAcciones as $accion) {
                    $valores[$accion] = $permitido && in_array($accion, $accionesModulo, true);
                }

                DB::table('permisos')->updateOrInsert(
                    ['role_id' => $role->id, 'modulo' => $modulo],
                    $valores + ['updated_at' => $ahora, 'created_at' => $ahora]
                );
            }
        }
    }

    public function down(): void
    {
        DB::table('permisos')->truncate();
    }
};
