<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dependencias', function (Blueprint $table) {
            $table->string('nombre_interesado')->nullable()->after('correo');
            $table->string('cedula_interesado', 30)->nullable()->after('nombre_interesado');
            $table->string('enlace_drive')->nullable()->after('cedula_interesado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dependencias', function (Blueprint $table) {
            $table->dropColumn(['nombre_interesado', 'cedula_interesado', 'enlace_drive']);
        });
    }
};
