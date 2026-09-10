<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sesiones', 'id_docente')) {
            Schema::table('sesiones', function (Blueprint $table) {
                $table->foreignId('id_docente')->nullable()->after('id_curso')->constrained('docentes')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sesiones', 'id_docente')) {
            Schema::table('sesiones', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_docente');
            });
        }
    }
};
