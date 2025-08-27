<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener todos los productos con imágenes
        $products = DB::table('products')->whereNotNull('main_image')->get();
        
        $updatedCount = 0;
        
        foreach ($products as $product) {
            $updates = [];
            
            // Actualizar main_image
            if ($product->main_image) {
                $newMainImage = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $product->main_image);
                if ($newMainImage !== $product->main_image) {
                    $updates['main_image'] = $newMainImage;
                }
            }
            
            // Actualizar gallery (si es JSON)
            if ($product->gallery) {
                $newGallery = preg_replace('/\.(jpg|jpeg|png|gif)/i', '.webp', $product->gallery);
                if ($newGallery !== $product->gallery) {
                    $updates['gallery'] = $newGallery;
                }
            }
            
            // Aplicar actualizaciones si hay cambios
            if (!empty($updates)) {
                DB::table('products')->where('id', $product->id)->update($updates);
                $updatedCount++;
            }
        }
        
        echo "✅ {$updatedCount} productos actualizados con rutas WebP\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Para revertir, necesitaríamos conocer las extensiones originales
        // Por simplicidad, no implementamos el rollback ya que no es seguro
        echo "❌ No es posible revertir esta migración de forma automática\n";
    }
};
