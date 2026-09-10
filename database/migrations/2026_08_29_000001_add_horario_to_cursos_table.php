<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cursos', 'dias_semana')) {
            return;
        }

        Schema::table('cursos', function (Blueprint $table) {
            $table->json('dias_semana')->nullable()->after('cupo_max');
            $table->time('hora_inicio')->nullable()->after('dias_semana');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn(['dias_semana', 'hora_inicio', 'hora_fin']);
        });
    }
};
