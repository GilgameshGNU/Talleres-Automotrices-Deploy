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
        Schema::create('notificacion', function (Blueprint $table) {
            $table->integer('idNotificacion', true);
            $table->integer('idOrden')->nullable();
            $table->integer('idUsuario');
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->dateTime('fecha')->useCurrent();
            $table->timestamps();

            $table->foreign('idOrden')->references('idOrden')->on('ordentrabajo')->onDelete('cascade');
            $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
