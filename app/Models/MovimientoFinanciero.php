<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoFinanciero extends Model
{
    use SoftDeletes;

    protected $table = 'movimientos_financieros';

    protected $fillable = [
        'id_ingreso',
        'id_egreso',
        'tipo',
        'monto',
        'fecha',
        'concepto',
        'mes_ano',
    ];

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class, 'id_ingreso');
    }

    public function egreso(): BelongsTo
    {
        return $this->belongsTo(Egreso::class, 'id_egreso');
    }
}
