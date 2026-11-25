<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('ocupacion')->nullable();
            $table->text('intereses')->nullable();
            $table->text('preferencias')->nullable();
            $table->text('contexto_personal')->nullable();
            $table->boolean('memoria_activa')->default(false);
            $table->timestamps();
        });

        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('titulo')->default('Nueva conversacion');
            $table->string('categoria')->default('General');
            $table->timestamps();
        });

        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')
                ->constrained('conversaciones')
                ->cascadeOnDelete();

            $table->string('rol', 20);
            $table->text('contenido');
            $table->timestamps();
        });

        Schema::create('objetivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->default('General');
            $table->string('estado')->default('Pendiente');
            $table->unsignedTinyInteger('progreso')->default(0);
            $table->date('fecha_limite')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objetivos');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('conversaciones');
        Schema::dropIfExists('perfiles');
    }
};