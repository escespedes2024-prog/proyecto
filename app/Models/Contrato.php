<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use SoftDeletes;

    protected $table = 'contratos';

    const TIPO_SALARIO = 'Salario';
    const TIPO_BONO = 'Bono';
    const TIPO_BENEFICIOS = 'Beneficios';
    const TIPO_VOLUNTARIO = 'Voluntario';

    public static function tiposCompensacion(): array
    {
        return [
            self::TIPO_SALARIO => 'Salario',
            self::TIPO_BONO => 'Bono',
            self::TIPO_BENEFICIOS => 'Beneficios',
            self::TIPO_VOLUNTARIO => 'Voluntario',
        ];
    }

    public function esVoluntario(): bool
    {
        return $this->tipo_compensacion === self::TIPO_VOLUNTARIO;
    }

    protected $fillable = [
        'id_miembro',
        'id_lider_iglesia',
        'tipo_compensacion',
        'salario',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'id_miembro');
    }

    public function lider(): BelongsTo
    {
        return $this->belongsTo(LiderIglesia::class, 'id_lider_iglesia');
    }

    public function egresos(): HasMany
    {
        return $this->hasMany(Egreso::class, 'id_contrato');
    }

    public function getVigenciaAttribute(): string
    {
        if ($this->trashed()) {
            return 'Eliminado';
        }

        $hoy = now()->startOfDay();

        if (Carbon::parse($this->fecha_inicio)->gt($hoy)) {
            return 'Futuro';
        }

        if (!$this->fecha_fin) {
            return 'Vigente';
        }

        $fin = Carbon::parse($this->fecha_fin);

        if ($fin->lt($hoy)) {
            return 'Vencido';
        }

        if ($fin->lte($hoy->copy()->addDays(30))) {
            return 'Por vencer';
        }

        return 'Vigente';
    }

    public function getVigenteAttribute(): bool
    {
        return in_array($this->vigencia, ['Vigente', 'Por vencer']);
    }

    public static function tieneSolapamiento(int $miembroId, string $fechaInicio, ?string $fechaFin, ?int $ignorarId = null): bool
    {
        $query = self::where('id_miembro', $miembroId)
            ->where('fecha_inicio', '<=', $fechaFin ?? '9999-12-31')
            ->where(function ($q) use ($fechaInicio) {
                $q->whereNull('fecha_fin')
                  ->orWhere('fecha_fin', '>=', $fechaInicio);
            });

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }
}
