<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ministerio extends Model
{
    use SoftDeletes;

    protected $table = 'ministerios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'liderable_id',
        'liderable_type',
    ];

    /**
     * Relación polimórfica para el líder del ministerio.
     * Puede ser Miembro o LiderIglesia.
     */
    public function liderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(Miembro::class, 'miembro_ministerio')
                    ->withPivot(['cargo_id', 'fecha_inicio', 'fecha_fin'])
                    ->withTimestamps();
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'id_ministerio');
    }
}
