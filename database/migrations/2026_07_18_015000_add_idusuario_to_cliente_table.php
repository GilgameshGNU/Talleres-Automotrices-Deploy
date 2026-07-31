<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('cliente', 'idUsuario')) {
            Schema::table('cliente', function (Blueprint $table) {
                $table->integer('idUsuario')->nullable()->after('idCliente');
                $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cliente', 'idUsuario')) {
            Schema::table('cliente', function (Blueprint $table) {
                $table->dropForeign(['idUsuario']);
                $table->dropColumn('idUsuario');
            });
        }
    }
};

