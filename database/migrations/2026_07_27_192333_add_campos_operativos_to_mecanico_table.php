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
        Schema::table('mecanico', function (Blueprint $table) {
            $table->integer('idUsuario')->nullable()->after('idMecanico');
            $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->onDelete('set null');
            $table->tinyInteger('disponible')->default(1)->after('idSucursal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mecanico', function (Blueprint $table) {
            $table->dropForeign(['idUsuario']);
            $table->dropColumn(['idUsuario', 'disponible']);
        });
    }
};
