<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PagoCurso extends Model
{
    use SoftDeletes;

    protected $table = 'pago_cursos';

    protected $fillable = [
        'inscripcion_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'comprobante',
        'registrado_por',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function ingreso(): HasOne
    {
        return $this->hasOne(Ingreso::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
