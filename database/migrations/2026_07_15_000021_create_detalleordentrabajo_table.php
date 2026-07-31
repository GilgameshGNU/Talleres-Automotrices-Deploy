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
        Schema::create('detalleordentrabajo', function (Blueprint $table) {
            $table->integer('idDetalle', true);
            $table->integer('idOrden');
            $table->integer('idServicio')->nullable();
            $table->integer('idRepuesto')->nullable();
            $table->integer('cantidad')->default(1);
            $table->decimal('precioUnitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('observaciones')->nullable();

            $table->foreign('idOrden')->references('idOrden')->on('ordentrabajo')->onDelete('cascade');
            $table->foreign('idServicio')->references('idServicio')->on('servicio');
            $table->foreign('idRepuesto')->references('idRepuesto')->on('repuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalleordentrabajo');
    }
};
