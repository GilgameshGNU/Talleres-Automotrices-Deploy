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
        Schema::create('cita', function (Blueprint $table) {
            $table->integer('idCita', true);
            $table->integer('idCliente');
            $table->integer('idVehiculo');
            $table->integer('idMecanico')->nullable();
            $table->date('fecha');
            $table->time('hora');
            $table->string('estado', 20)->default('Pendiente')->comment('Pendiente, Confirmada, Cancelada, Completada');
            $table->string('motivo', 255)->nullable();

            $table->foreign('idCliente')->references('idCliente')->on('cliente');
            $table->foreign('idVehiculo')->references('idVehiculo')->on('vehiculo');
            $table->foreign('idMecanico')->references('idMecanico')->on('mecanico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};
