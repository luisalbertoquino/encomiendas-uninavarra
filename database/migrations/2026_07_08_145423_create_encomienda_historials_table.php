<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomienda_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encomienda_id')->constrained('encomiendas')->cascadeOnDelete();
            $table->enum('estado', ['recibida', 'notificada', 'entregada']);
            $table->string('por')->nullable();
            $table->dateTime('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomienda_historial');
    }
};
