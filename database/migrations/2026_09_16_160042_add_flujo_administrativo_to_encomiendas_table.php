<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('encomiendas', function (Blueprint $table) {
            $table->foreignId('colaborador_id')->nullable()->after('id')->constrained('colaboradores')->nullOnDelete();
            $table->string('reasignada_por')->nullable()->after('entregado_a');
            $table->dateTime('reasignada_at')->nullable()->after('reasignada_por');
        });

        DB::statement("ALTER TABLE encomiendas MODIFY estado ENUM('recibida', 'en_administrativa', 'notificada', 'entregada') NOT NULL DEFAULT 'recibida'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE encomiendas MODIFY estado ENUM('recibida', 'notificada', 'entregada') NOT NULL DEFAULT 'recibida'");

        Schema::table('encomiendas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('colaborador_id');
            $table->dropColumn(['reasignada_por', 'reasignada_at']);
        });
    }
};
