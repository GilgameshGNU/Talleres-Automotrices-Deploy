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
        Schema::create('diagnostico', function (Blueprint $table) {
            $table->integer('idDiagnostico', true);
            $table->integer('idOrden');
            $table->text('descripcion');
            $table->dateTime('fecha')->useCurrent();
            $table->timestamps();

            $table->foreign('idOrden')->references('idOrden')->on('ordentrabajo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostico');
    }
};
