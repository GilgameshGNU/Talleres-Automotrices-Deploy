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
        Schema::create('mecanico_especialidad', function (Blueprint $table) {
            $table->integer('idMecanico');
            $table->integer('idEspecialidad');

            $table->primary(['idMecanico', 'idEspecialidad']);
            $table->foreign('idMecanico')->references('idMecanico')->on('mecanico')->onDelete('cascade');
            $table->foreign('idEspecialidad')->references('idEspecialidad')->on('especialidad')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mecanico_especialidad');
    }
};
