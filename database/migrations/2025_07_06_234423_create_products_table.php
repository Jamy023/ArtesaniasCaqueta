<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Nombre del producto
            $table->string('slug')->unique();          // URL amigable
            $table->text('description');               // Descripción del producto
            $table->decimal('price', 10, 2);           // Precio (8 dígitos, 2 decimales)
            $table->integer('stock')->default(0);      // Cantidad en inventario
            $table->string('sku')->unique();           // Código único del producto
            $table->json('dimensions')->nullable();     // Dimensiones (alto, ancho, largo)
            $table->string('main_image');              // Imagen principal
            $table->json('gallery')->nullable();       // Galería de imágenes adicionales
            $table->boolean('is_active')->default(true); // Si está activo
            $table->foreignId('category_id')->constrained(); // Llave foránea a categories
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};