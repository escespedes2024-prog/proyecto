<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    use SoftDeletes;

    protected $table = 'ingresos';

    protected $fillable = [
        'id_actividad',
        'id_culto',
        'pago_curso_id',
        'monto_total',
        'fecha',
        'tipo',
        'metodo_pago',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'id_actividad');
    }

    public function culto(): BelongsTo
    {
        return $this->belongsTo(Culto::class, 'id_culto');
    }

    public function pagoCurso(): BelongsTo
    {
        return $this->belongsTo(PagoCurso::class, 'pago_curso_id');
    }
}
