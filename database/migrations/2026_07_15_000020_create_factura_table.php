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
        Schema::create('factura', function (Blueprint $table) {
            $table->integer('idFactura', true);
            $table->integer('idOrden');
            $table->string('nroFactura', 50)->unique();
            $table->date('fechaEmision');
            $table->decimal('montoTotal', 12, 2);
            $table->string('nitCliente', 20);

            $table->foreign('idOrden')->references('idOrden')->on('ordentrabajo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura');
    }
};
