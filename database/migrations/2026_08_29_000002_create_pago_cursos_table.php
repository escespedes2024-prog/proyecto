<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pago_cursos')) {
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
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_cursos');
    }
};
