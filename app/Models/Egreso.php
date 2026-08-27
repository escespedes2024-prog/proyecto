<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Egreso extends Model
{
    use SoftDeletes;

    protected $table = 'egresos';

    protected $fillable = [
        'id_lider_iglesia',
        'id_contrato',
        'tipo_egreso',
        'monto',
        'fecha',
        'descripcion',
        'responsable',
        'comprobante',
    ];

    public function liderIglesia(): BelongsTo
    {
        return $this->belongsTo(LiderIglesia::class, 'id_lider_iglesia');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }
}
