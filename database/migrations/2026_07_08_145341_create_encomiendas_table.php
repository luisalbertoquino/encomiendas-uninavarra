<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomiendas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();

            // Registro (pestaña "Registrar")
            $table->dateTime('fecha');
            $table->string('tipo')->default('Paquete');
            $table->string('descripcion');
            $table->string('remitente')->nullable();
            $table->string('guia')->nullable();
            $table->string('recibe');
            $table->string('interesado');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->nullOnDelete();
            $table->string('whatsapp', 20)->nullable();
            $table->string('correo')->nullable();
            $table->text('obs')->nullable();

            // Consulta pública sin login: código + documento del interesado
            $table->string('documento_interesado', 30)->nullable();

            // Enlace externo en vez de archivo subido al servidor
            $table->string('enlace_drive')->nullable();

            // Estado y entrega
            $table->enum('estado', ['recibida', 'notificada', 'entregada'])->default('recibida');
            $table->string('entregado_a')->nullable();
            $table->dateTime('fecha_entrega')->nullable();

            $table->timestamps();

            $table->index('estado');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomiendas');
    }
};
