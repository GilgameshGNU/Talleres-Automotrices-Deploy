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
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('idUsuario', true);
            $table->integer('idRol');
            $table->string('nombreUsuario', 50)->unique();
            $table->string('contrasena', 255);
            $table->string('email', 100)->unique();
            $table->tinyInteger('estado')->default(1);

            $table->foreign('idRol')->references('idRol')->on('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
