<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoActividad extends Model
{
    use SoftDeletes;

    protected $table = 'seguimiento_actividades';

    protected $fillable = [
        'id_actividad',
        'fecha_registro',
        'observacion',
        'responsable',
        'porcentaje_avance',
        'estado',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'id_actividad');
    }
}
