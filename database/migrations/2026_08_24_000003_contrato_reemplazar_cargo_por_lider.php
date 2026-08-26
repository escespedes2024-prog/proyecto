<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('id_lider_iglesia')->nullable()->after('id_miembro')->constrained('lideres_iglesia')->onDelete('set null');
        });

        Schema::table('contratos', function (Blueprint $table) {
            $table->dropForeign(['id_cargo']);
            $table->dropColumn('id_cargo');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('id_cargo')->nullable()->after('id_lider_iglesia')->constrained('cargos')->onDelete('set null');
        });

        Schema::table('contratos', function (Blueprint $table) {
            $table->dropForeign(['id_lider_iglesia']);
            $table->dropColumn('id_lider_iglesia');
        });
    }
};
