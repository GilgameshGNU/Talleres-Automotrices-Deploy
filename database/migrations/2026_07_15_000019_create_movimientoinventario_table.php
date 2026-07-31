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
        Schema::create('movimientoinventario', function (Blueprint $table) {
            $table->integer('idMovimiento', true);
            $table->integer('idInventario');
            $table->string('tipo', 20)->comment('Ej: Entrada, Salida, Ajuste');
            $table->integer('cantidad');
            $table->dateTime('fecha')->useCurrent();
            $table->string('motivo', 255)->nullable();

            $table->foreign('idInventario')->references('idInventario')->on('inventario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientoinventario');
    }
};
