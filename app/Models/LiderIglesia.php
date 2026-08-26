<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LiderIglesia extends Model
{
    use SoftDeletes;

    protected $table = 'lideres_iglesia';

    protected $fillable = [
        'id_cargo',
        'nombre',
        'telefono',
        'fecha_inicio',
        'activo',
    ];

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function cultos(): HasMany
    {
        return $this->hasMany(Culto::class, 'id_lider_iglesia');
    }

    public function egresos(): HasMany
    {
        return $this->hasMany(Egreso::class, 'id_lider_iglesia');
    }

    public function ministerios(): MorphMany
    {
        return $this->morphMany(Ministerio::class, 'liderable');
    }
}
