<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\LiderIglesiaController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\MinisterioController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\SeguimientoActividadController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\CultoController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PagoCursoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;

// Ruta de inicio muestra la bienvenida; si ya está autenticado va al dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// Rutas de Autenticación (Invitados) — el envío del formulario va limitado
// por intentos para mitigar fuerza bruta (OWASP A07).
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:register');
});

// Rutas Protegidas por Autenticación
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reportes (accesible para cualquier usuario autenticado)
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/miembros', [ReporteController::class, 'miembros'])->name('reportes.miembros');
    Route::get('reportes/asistencia', [ReporteController::class, 'asistencia'])->name('reportes.asistencia');
    Route::get('reportes/financiero', [ReporteController::class, 'financiero'])->name('reportes.financiero');

    // Acceso y Seguridad (solo Administrador)
    Route::middleware('role:Administrador')->group(function () {
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::resource('users', UserController::class);

        Route::get('roles/assign', [RoleController::class, 'userRoles'])->name('roles.user');
        Route::get('roles/assign/{user}/edit', [RoleController::class, 'userRolesEdit'])->name('roles.user.edit');
        Route::post('roles/assign', [RoleController::class, 'assignRole'])->name('roles.user.save');
        Route::post('roles/assign/{id}/clear', [RoleController::class, 'clearUserRoles'])->name('roles.user.clear');
        Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
        Route::resource('roles', RoleController::class);
    });

    // Parametrización y módulos operativos (Administrador | Secretario)
    Route::middleware('role:Administrador,Secretario')->group(function () {
        // 1. Cargos
        Route::post('cargos/{id}/restore', [CargoController::class, 'restore'])->name('cargos.restore');
        Route::resource('cargos', CargoController::class);

        // 2. Líderes de Iglesia
        Route::post('lideres/{id}/restore', [LiderIglesiaController::class, 'restore'])->name('lideres.restore');
        Route::resource('lideres', LiderIglesiaController::class);

        // 3. Miembros
        Route::post('miembros/{id}/restore', [MiembroController::class, 'restore'])->name('miembros.restore');
        Route::resource('miembros', MiembroController::class);

        // 4. Ministerios
        Route::post('ministerios/{id}/restore', [MinisterioController::class, 'restore'])->name('ministerios.restore');
        Route::resource('ministerios', MinisterioController::class);

        // 5. Actividades
        Route::post('actividades/{id}/restore', [ActividadController::class, 'restore'])->name('actividades.restore');
        Route::resource('actividades', ActividadController::class);

        // 6. Seguimiento de Actividades
        Route::post('seguimiento/{id}/restore', [SeguimientoActividadController::class, 'restore'])->name('seguimiento.restore');
        Route::resource('seguimiento', SeguimientoActividadController::class);

        // 7. Cursos
        Route::post('cursos/{id}/restore', [CursoController::class, 'restore'])->name('cursos.restore');
        Route::resource('cursos', CursoController::class);

        // 7.1 Inscripciones a Cursos (módulo aparte)
        Route::get('inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');
        Route::get('inscripciones/{curso}/gestionar', [InscripcionController::class, 'gestion'])->name('inscripciones.gestion');
        Route::get('inscripciones/{inscripcion}/edit', [InscripcionController::class, 'edit'])->name('inscripciones.edit');
        Route::put('inscripciones/{inscripcion}', [InscripcionController::class, 'update'])->name('inscripciones.update');
        Route::post('cursos/{curso}/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');
        Route::post('inscripciones/{id}/restore', [InscripcionController::class, 'restore'])->name('inscripciones.restore');
        Route::delete('inscripciones/{inscripcion}', [InscripcionController::class, 'destroy'])->name('inscripciones.destroy');

        // 7.2 Pagos de Cursos (generan Ingreso automáticamente)
        Route::get('inscripciones/{inscripcion}/pagos/create', [PagoCursoController::class, 'create'])->name('pagos.create');
        Route::post('inscripciones/{inscripcion}/pagos', [PagoCursoController::class, 'store'])->name('pagos.store');
        Route::post('pagos/{id}/restore', [PagoCursoController::class, 'restore'])->name('pagos.restore');
        Route::delete('pagos/{pago}', [PagoCursoController::class, 'destroy'])->name('pagos.destroy');

        // 8. Docentes
        Route::post('docentes/{id}/restore', [DocenteController::class, 'restore'])->name('docentes.restore');
        Route::resource('docentes', DocenteController::class);

        // 9. Sesiones
        Route::post('cursos/{curso}/sesiones/generar', [SesionController::class, 'generar'])->name('sesiones.generar');
        Route::get('miembros/{miembroId}/asistencia', [AsistenciaController::class, 'historial'])->name('asistencia.historial');
        Route::post('sesiones/{id}/restore', [SesionController::class, 'restore'])->name('sesiones.restore');
        Route::resource('sesiones', SesionController::class);

        // 9b. Asistencia (módulo propio)
        Route::get('asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
        Route::get('asistencia/registrar', [AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
        Route::post('asistencia/registrar/{sesion}', [AsistenciaController::class, 'store'])->name('asistencia.store');
        Route::post('asistencia/registrar/{sesion}/marcar-todos', [AsistenciaController::class, 'marcarTodos'])->name('asistencia.marcarTodos');
    });

    // Finanzas (Administrador | Tesorero)
    Route::middleware('role:Administrador,Tesorero')->group(function () {
        // 10. Cultos
        Route::post('cultos/{id}/restore', [CultoController::class, 'restore'])->name('cultos.restore');
        Route::resource('cultos', CultoController::class);

        // 11. Ingresos
        Route::post('ingresos/{id}/restore', [IngresoController::class, 'restore'])->name('ingresos.restore');
        Route::resource('ingresos', IngresoController::class);

        // 12. Egresos
        Route::post('egresos/{id}/restore', [EgresoController::class, 'restore'])->name('egresos.restore');
        Route::resource('egresos', EgresoController::class);

        // 13. Contratos
        Route::get('contratos/{id}/renovar', [ContratoController::class, 'renovar'])->name('contratos.renovar');
        Route::post('contratos/{id}/restore', [ContratoController::class, 'restore'])->name('contratos.restore');
        Route::resource('contratos', ContratoController::class);
    });
});