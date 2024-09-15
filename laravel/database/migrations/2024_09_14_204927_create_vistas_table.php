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
        Schema::create('vistas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('propuesta_id');
            $table->text('URL_imagenes');
            $table->date('fecha_envio');
            $table->text('descripcion');

            $table->foreign('propuesta_id')->references('id')->on('propuestas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vistas');
    }
};
