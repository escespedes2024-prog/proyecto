<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Inscripcion;
use App\Models\Miembro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReactivacionRegistrosTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Role::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['estado' => 'Activo', 'nivel_acceso' => 1]
        );

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($rol->id);
    }

    private function miembro(string $nombre = 'Miembro de Prueba'): Miembro
    {
        return Miembro::create([
            'nombre' => $nombre,
            'fecha_ingreso' => now()->toDateString(),
            'estado' => 'Activo',
        ]);
    }

    private function curso(): Curso
    {
        return Curso::create([
            'nombre' => 'Curso de Prueba',
            'f_inicio' => now()->toDateString(),
            'cupo_max' => 10,
        ]);
    }

    public function test_reinscribir_restaura_la_inscripcion_borrada(): void
    {
        $miembro = $this->miembro();
        $curso = $this->curso();

        $inscripcion = Inscripcion::create([
            'miembro_id' => $miembro->id,
            'curso_id' => $curso->id,
            'fecha_inscripcion' => now()->subWeek()->toDateString(),
            'estado' => 'Activo',
        ]);

        $inscripcion->delete();

        $this->actingAs($this->admin)
            ->post(route('inscripciones.store', $curso), [
                'miembro_id' => $miembro->id,
                'fecha_inscripcion' => now()->toDateString(),
            ])
            ->assertRedirect(route('inscripciones.gestion', $curso))
            ->assertSessionHas('success', 'Se reactivó la inscripción existente.');

        $this->assertDatabaseCount('inscripciones', 1);

        $reactivada = Inscripcion::findOrFail($inscripcion->id);

        $this->assertNull($reactivada->deleted_at);
        $this->assertSame('Activo', $reactivada->estado);
        $this->assertSame(now()->toDateString(), $reactivada->fecha_inscripcion);
    }

    public function test_no_duplica_una_inscripcion_activa(): void
    {
        $miembro = $this->miembro();
        $curso = $this->curso();

        Inscripcion::create([
            'miembro_id' => $miembro->id,
            'curso_id' => $curso->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado' => 'Activo',
        ]);

        $this->actingAs($this->admin)
            ->post(route('inscripciones.store', $curso), [
                'miembro_id' => $miembro->id,
                'fecha_inscripcion' => now()->toDateString(),
            ])
            ->assertSessionHas('error', 'El miembro ya está inscrito en este curso.');

        $this->assertDatabaseCount('inscripciones', 1);
    }

    public function test_registrar_docente_reactiva_el_docente_borrado(): void
    {
        $miembro = $this->miembro();

        $docente = Docente::create([
            'id_miembro' => $miembro->id,
            'especialidad' => 'Matemática',
            'activo' => true,
        ]);

        $docente->delete();

        $this->actingAs($this->admin)
            ->post(route('docentes.store'), [
                'id_miembro' => $miembro->id,
                'especialidad' => 'Física',
                'titulo' => 'Licenciado',
                'activo' => 1,
            ])
            ->assertRedirect(route('docentes.index'))
            ->assertSessionHas('success', 'Se reactivó el docente existente.');

        $this->assertDatabaseCount('docentes', 1);

        $reactivado = Docente::findOrFail($docente->id);

        $this->assertNull($reactivado->deleted_at);
        $this->assertSame('Física', $reactivado->especialidad);
    }
}
