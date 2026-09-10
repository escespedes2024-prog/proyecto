<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ingresos', 'pago_curso_id')) {
            Schema::table('ingresos', function (Blueprint $table) {
                $table->foreignId('pago_curso_id')->nullable()->after('id_culto')->constrained('pago_cursos')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ingresos', 'pago_curso_id')) {
            Schema::table('ingresos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('pago_curso_id');
            });
        }
    }
};
