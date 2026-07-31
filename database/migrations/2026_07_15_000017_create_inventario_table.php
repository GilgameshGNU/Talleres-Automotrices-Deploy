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
        Schema::create('inventario', function (Blueprint $table) {
            $table->integer('idInventario', true);
            $table->integer('idSucursal');
            $table->integer('idRepuesto');
            $table->integer('stockActual')->default(0);
            $table->integer('stockMinimo')->default(0);
            $table->string('ubicacion', 100)->nullable();

            $table->foreign('idSucursal')->references('idSucursal')->on('sucursal');
            $table->foreign('idRepuesto')->references('idRepuesto')->on('repuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
