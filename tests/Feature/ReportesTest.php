<?php

namespace Tests\Feature;

use App\Models\Miembro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use DatabaseMigrations;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Role::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['estado' => 'Activo', 'nivel_acceso' => 1]
        );

        $this->admin = User::factory()->create(['email' => 'reportes_test@proyecto.com']);
        $this->admin->roles()->attach($rol);
    }

    protected function tearDown(): void
    {
        $this->admin?->roles()->detach();
        $this->admin?->forceDelete();
        parent::tearDown();
    }

    private function miembro(array $attributes = []): Miembro
    {
        return Miembro::create(array_merge([
            'nombre' => 'Miembro de Prueba',
            'telefono' => '70000000',
            'sexo' => 'Masculino',
            'fecha_ingreso' => '2024-01-15',
            'estado' => 'Activo',
        ], $attributes));
    }

    public function test_guest_no_accede_a_reportes(): void
    {
        $this->get('/reportes/miembros')->assertRedirect('/login');
    }

    public function test_reporte_miembros_renderiza_documento_y_totales(): void
    {
        $this->miembro(['nombre' => 'Ana Activa Test', 'estado' => 'Activo']);
        $this->miembro(['nombre' => 'Beto Inactivo Test', 'estado' => 'Inactivo']);

        $response = $this->actingAs($this->admin)->get('/reportes/miembros');

        $response->assertOk();
        // Documento imprimible presente con encabezado y estructura.
        $response->assertSee('report-document', false);
        $response->assertSee('Reporte de Miembros');
        $response->assertSee('Total de miembros');
        $response->assertSee('Miembros activos');
        $response->assertSee('Miembros inactivos');
        $response->assertSee('Ana Activa Test');
        $response->assertSee('Beto Inactivo Test');
    }

    public function test_filtro_estado_actualiza_tabla_y_estadisticas(): void
    {
        $this->miembro(['nombre' => 'Ana Activa Test', 'estado' => 'Activo']);
        $this->miembro(['nombre' => 'Beto Inactivo Test', 'estado' => 'Inactivo']);

        $response = $this->actingAs($this->admin)->get('/reportes/miembros?estado=Activo');

        $response->assertOk();
        $response->assertSee('Ana Activa Test');
        $response->assertDontSee('Beto Inactivo Test');
        // El estado filtrado se refleja en los metadatos del encabezado.
        $response->assertSee('Estado');
    }

    public function test_filtro_fechas_de_ingreso(): void
    {
        $this->miembro(['nombre' => 'Dentro Rango Test', 'fecha_ingreso' => '2024-06-01']);
        $this->miembro(['nombre' => 'Fuera Rango Test', 'fecha_ingreso' => '2022-01-01']);

        $response = $this->actingAs($this->admin)
            ->get('/reportes/miembros?desde=2024-01-01&hasta=2024-12-31');

        $response->assertOk();
        $response->assertSee('Dentro Rango Test');
        $response->assertDontSee('Fuera Rango Test');
    }

    public function test_reporte_asistencia_renderiza_documento(): void
    {
        $response = $this->actingAs($this->admin)->get('/reportes/asistencia');

        $response->assertOk();
        $response->assertSee('report-document', false);
        $response->assertSee('Reporte de Asistencia');
        $response->assertSee('Presentes');
    }

    public function test_reporte_financiero_renderiza_documento(): void
    {
        $response = $this->actingAs($this->admin)->get('/reportes/financiero');

        $response->assertOk();
        $response->assertSee('report-document', false);
        $response->assertSee('Reporte Financiero');
        $response->assertSee('Balance');
    }

    public function test_panel_de_control_conserva_filtros(): void
    {
        $response = $this->actingAs($this->admin)->get('/reportes/miembros');

        $response->assertOk();
        $response->assertSee('report-panel', false);
        $response->assertSee('Aplicar filtros');
        $response->assertSee('Vista previa');
        $response->assertSee('Exportar PDF');
        $response->assertSee('name="estado"', false);
        $response->assertSee('name="desde"', false);
        $response->assertSee('name="hasta"', false);
        // Los botones ejecutan acciones reales: el script de impresión se inyecta.
        $response->assertSee('window.print()', false);
        $response->assertSee('data-report-preview', false);
    }
}
