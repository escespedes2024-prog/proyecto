<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    use SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'id_ministerio',
        'nombre',
        'fecha',
        'lugar',
        'descripcion',
        'estado',
    ];

    public function ministerio(): BelongsTo
    {
        return $this->belongsTo(Ministerio::class, 'id_ministerio');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoActividad::class, 'id_actividad');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'id_actividad');
    }
}
