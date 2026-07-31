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
        Schema::create('repuesto', function (Blueprint $table) {
            $table->integer('idRepuesto', true);
            $table->integer('idProveedor')->nullable();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->decimal('precioVenta', 10, 2);
            $table->string('marca', 50)->nullable();

            $table->foreign('idProveedor')->references('idProveedor')->on('proveedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repuesto');
    }
};
