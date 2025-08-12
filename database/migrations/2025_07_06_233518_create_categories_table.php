<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                    // ID autoincremental
            $table->string('name');          // Nombre: "Abanicos", "Portaplumas"
            $table->string('slug')->unique(); // URL amigable: "abanicos", "portaplumas"
            $table->text('description')->nullable(); // Descripción opcional
            $table->string('image')->nullable();     // Imagen opcional
            $table->boolean('is_active')->default(true); // Si está activa
            $table->timestamps();            // created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};