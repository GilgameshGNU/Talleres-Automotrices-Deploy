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
        Schema::create('ordentrabajo', function (Blueprint $table) {
            $table->integer('idOrden', true);
            $table->integer('idCliente');
            $table->integer('idVehiculo')->nullable();
            $table->integer('idMecanico')->nullable();
            $table->date('fechaIngreso');
            $table->date('fechaEntrega')->nullable();
            $table->string('estado', 50)->default('Pendiente');
            $table->text('diagnostico')->nullable();
            $table->decimal('total', 12, 2)->default(0.00);

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
        Schema::dropIfExists('ordentrabajo');
    }
};
