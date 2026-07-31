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
        Schema::table('ordentrabajo', function (Blueprint $table) {
            $table->time('horaInicio')->nullable()->after('fechaIngreso');
            $table->dateTime('horaFinEstimada')->nullable()->after('horaInicio');
            $table->dateTime('horaFinReal')->nullable()->after('horaFinEstimada');
            $table->string('etapa', 30)->default('Recibido')->after('estado');
            // etapa: Recibido | Diagnóstico | En reparación | Terminado
            $table->string('sucursal', 100)->nullable()->after('etapa');
            $table->string('servicioSolicitado', 255)->nullable()->after('diagnostico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordentrabajo', function (Blueprint $table) {
            $table->dropColumn(['horaInicio', 'horaFinEstimada', 'horaFinReal', 'etapa', 'sucursal', 'servicioSolicitado']);
        });
    }
};
