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
        Schema::create('modelovehiculo', function (Blueprint $table) {
            $table->integer('idModelo', true);
            $table->integer('idMarca');
            $table->string('nombre', 100);
            $table->string('tipoMotor', 50)->nullable();

            $table->foreign('idMarca')->references('idMarca')->on('marcavehiculo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelovehiculo');
    }
};
