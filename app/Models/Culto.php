<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Culto extends Model
{
    use SoftDeletes;

    protected $table = 'cultos';

    protected $fillable = [
        'id_lider_iglesia',
        'nombre',
        'dia_semana',
        'hora',
        'descripcion',
    ];

    public function liderIglesia(): BelongsTo
    {
        return $this->belongsTo(LiderIglesia::class, 'id_lider_iglesia');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'id_culto');
    }
}
