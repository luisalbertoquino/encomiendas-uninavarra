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
        Schema::table('encomiendas', function (Blueprint $table) {
            $table->foreignId('interesado_id')->nullable()->after('colaborador_id')->constrained('interesados')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encomiendas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('interesado_id');
        });
    }
};
