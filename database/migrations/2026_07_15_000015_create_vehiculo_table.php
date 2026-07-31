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
        Schema::create('vehiculo', function (Blueprint $table) {
            $table->integer('idVehiculo', true);
            $table->integer('idCliente');
            $table->integer('idModelo');
            $table->string('placa', 15)->unique();
            $table->integer('anio');
            $table->string('color', 30)->nullable();
            $table->integer('kilometraje')->default(0);

            $table->foreign('idCliente')->references('idCliente')->on('cliente');
            $table->foreign('idModelo')->references('idModelo')->on('modelovehiculo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculo');
    }
};
