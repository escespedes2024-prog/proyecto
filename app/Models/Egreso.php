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
        'id_contrato',
        'tipo_egreso',
        'monto',
        'fecha',
        'descripcion',
        'responsable',
        'comprobante',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function liderIglesia()
    {
        return $this->hasOneThrough(
            LiderIglesia::class,
            Contrato::class,
            'id',
            'id',
            'id_contrato',
            'id_lider_iglesia'
        );
    }
}
