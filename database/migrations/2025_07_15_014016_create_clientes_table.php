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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->enum('tipo_documento', ['CC', 'TI', 'CE', 'PP', 'NIT'])->default('CC');
            $table->string('numero_documento')->unique();
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('departamento')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};