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
        Schema::table('ordentrabajo', function (Blueprint $table) {
            $table->integer('idVehiculo')->nullable()->change();
            $table->integer('idMecanico')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordentrabajo', function (Blueprint $table) {
            $table->integer('idVehiculo')->nullable(false)->change();
            $table->integer('idMecanico')->nullable(false)->change();
        });
    }
};
