<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago');
            $table->string('comprobante')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_actividad')->nullable()->constrained('actividades')->onDelete('set null');
            $table->foreignId('id_culto')->nullable()->constrained('cultos')->onDelete('set null');
            $table->foreignId('pago_curso_id')->nullable()->constrained('pago_cursos')->onDelete('set null');
            $table->decimal('monto_total', 12, 2);
            $table->date('fecha');
            $table->string('tipo'); // Diezmo, Ofrenda, Donación, Inscripción, etc.
            $table->string('metodo_pago'); // Efectivo, Transferencia, Tarjeta, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lider_iglesia')->nullable()->constrained('lideres_iglesia')->onDelete('set null');
            $table->foreignId('id_contrato')->nullable()->constrained('contratos')->onDelete('set null');
            $table->string('tipo_egreso'); // Servicio, Mantenimiento, Salario, Ayuda Social, etc.
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->text('descripcion')->nullable();
            $table->string('responsable');
            $table->string('comprobante')->nullable(); // Ruta o número de recibo/factura
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
        Schema::dropIfExists('ingresos');
        Schema::dropIfExists('pago_cursos');
    }
};
