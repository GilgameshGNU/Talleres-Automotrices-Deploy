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
        Schema::table('cliente', function (Blueprint $table) {
            $table->index('ci_nit');
            $table->index('telefono');
            $table->index('nombreCompleto');
        });

        Schema::table('ordentrabajo', function (Blueprint $table) {
            $table->index('estado');
            $table->index('idMecanico');
            $table->index('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente', function (Blueprint $table) {
            $table->dropIndex(['ci_nit']);
            $table->dropIndex(['telefono']);
            $table->dropIndex(['nombreCompleto']);
        });

        Schema::table('ordentrabajo', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['idMecanico']);
            $table->dropIndex(['idCliente']);
        });
    }
};
