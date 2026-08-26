<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sesion extends Model
{
    use SoftDeletes;

    protected $table = 'sesiones';

    protected $fillable = [
        'id_curso',
        'id_docente',
        'fecha',
        'hora',
        'tema',
        'observacion',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }

    public function miembrosAsistencia(): BelongsToMany
    {
        return $this->belongsToMany(Miembro::class, 'asistencias_sesion')
                    ->withPivot(['estado', 'observacion'])
                    ->withTimestamps();
    }
}
