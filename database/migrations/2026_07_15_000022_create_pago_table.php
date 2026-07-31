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
        Schema::create('pago', function (Blueprint $table) {
            $table->integer('idPago', true);
            $table->integer('idFactura');
            $table->integer('idMetodoPago');
            $table->decimal('monto', 12, 2);
            $table->dateTime('fecha')->useCurrent();
            $table->string('estado', 50)->default('Completado');

            $table->foreign('idFactura')->references('idFactura')->on('factura');
            $table->foreign('idMetodoPago')->references('idMetodoPago')->on('metodopago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago');
    }
};
