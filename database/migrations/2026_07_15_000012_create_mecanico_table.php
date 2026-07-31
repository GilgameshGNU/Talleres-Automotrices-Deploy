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
        Schema::create('mecanico', function (Blueprint $table) {
            $table->integer('idMecanico', true);
            $table->string('nombreCompleto', 150);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->integer('idSucursal');

            $table->foreign('idSucursal')->references('idSucursal')->on('sucursal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mecanico');
    }
};
