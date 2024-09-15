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
        Schema::create('propuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->text('descripcion');
            $table->string('etapa');
            $table->date('fecha_envio');
            $table->text('URL_propuesta');
            $table->unsignedBigInteger('id_mongo');
            $table->integer('status');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propuestas');
    }
};
