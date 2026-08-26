<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Docente extends Model
{
    use SoftDeletes;

    protected $table = 'docentes';

    protected $fillable = [
        'id_miembro',
        'especialidad',
        'titulo',
        'activo',
    ];

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'id_miembro');
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'docente_curso')->withTimestamps();
    }
}
