<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class Additional10ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener una categoría existente
        $category = Category::first();
        
        if (!$category) {
            $category = Category::create([
                'name' => 'Artesanías',
                'slug' => 'artesanias',
                'description' => 'Artesanías tradicionales del Caquetá',
                'is_active' => true
            ]);
        }

        // Lista de imágenes disponibles con sus nombres
        $images = [
            ['name' => 'Agenda Ecológica Personalizada', 'image' => 'agendas ecologicas personalizadas.webp', 'price' => 25000],
            ['name' => 'Aretes de Madera Natural', 'image' => 'aretes en madera.webp', 'price' => 18000],
            ['name' => 'Aretes Tipo Topo Escama de Pescado', 'image' => 'aretes tipo topo de escama pescado.webp', 'price' => 22000],
            ['name' => 'Barco de Madera con Caja Musical', 'image' => 'barcos de madera con caja musical.webp', 'price' => 85000],
            ['name' => 'Bolso Tejido con Detalles de Madera', 'image' => 'bolso tejido con madera.webp', 'price' => 65000],
            ['name' => 'Bolsos en Yute Pintados a Mano', 'image' => 'bolsos en yute pintados a mano.webp', 'price' => 45000],
            ['name' => 'Calendario Perpetuo de Madera', 'image' => 'calendario en madera.webp', 'price' => 35000],
            ['name' => 'Casa Porta Huevos Decorativa', 'image' => 'casa para huevos.webp', 'price' => 28000],
            ['name' => 'Cofre Mini de Bambú', 'image' => 'cofre mini bambu.webp', 'price' => 15000],
            ['name' => 'Set de Copas Aguardienteras', 'image' => 'copas aguardienteras.webp', 'price' => 55000],
        ];

        foreach ($images as $index => $item) {
            $baseSlug = \Str::slug($item['name']);
            $slug = $baseSlug . '-nuevo-' . ($index + 1);
            $sku = 'ART-NEW-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            
            // Verificar que el slug no exista
            if (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-artesanal-' . time() . '-' . ($index + 1);
            }
            
            Product::create([
                'name' => $item['name'] . ' (Nuevo Modelo)',
                'slug' => $slug,
                'sku' => $sku,
                'description' => 'Hermosa artesanía tradicional del Caquetá, elaborada a mano con materiales naturales de la región amazónica. Nueva colección con acabados mejorados.',
                'price' => $item['price'],
                'stock' => rand(10, 50),
                'main_image' => 'products/' . $item['image'],
                'gallery' => null,
                'category_id' => $category->id,
                'is_active' => true
            ]);
        }

        $this->command->info('✅ 10 productos adicionales creados exitosamente!');
    }
}
