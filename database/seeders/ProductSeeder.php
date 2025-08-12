<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Abanicos (category_id: 1)
            [
                'name' => 'Abanico Guacamaya 2',
                'slug' => 'abanico-guacamaya-2',
                'description' => 'Hermoso abanico artesanal con diseño de guacamaya, pintado a mano con colores vibrantes.',
                'price' => 25000,
                'stock' => 10,
                'sku' => 'ABA-GUA-002',
                'dimensions' => ['alto' => 30, 'ancho' => 15, 'profundidad' => 2],
                'main_image' => 'products/abanico-guacamaya-2.jpg',
                'gallery' => ['products/abanico-guacamaya-2-alt1.jpg', 'products/abanico-guacamaya-2-alt2.jpg'],
                'category_id' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Abanico Guacamaya 3',
                'slug' => 'abanico-guacamaya-3',
                'description' => 'Abanico artesanal con diseño tropical de guacamaya, ideal para regalo o decoración.',
                'price' => 28000,
                'stock' => 8,
                'sku' => 'ABA-GUA-003',
                'dimensions' => ['alto' => 32, 'ancho' => 16, 'profundidad' => 2],
                'main_image' => 'products/abanico-guacamaya-3.jpg',
                'gallery' => ['products/abanico-guacamaya-3-alt1.jpg'],
                'category_id' => 1,
                'is_active' => true
            ],
            // Bolsos (category_id: 2)
            [
                'name' => 'Bolso en Yute Pintado a Mano',
                'slug' => 'bolso-yute-pintado-mano',
                'description' => 'Bolso ecológico en yute con diseños únicos pintados a mano. Perfecto para el día a día.',
                'price' => 45000,
                'stock' => 15,
                'sku' => 'BOL-YUT-001',
                'dimensions' => ['alto' => 35, 'ancho' => 30, 'profundidad' => 10],
                'main_image' => 'products/bolsos-yute-pintados-mano.jpg',
                'gallery' => ['products/bolsos-yute-pintados-mano-2.jpg', 'products/bolsos-yute-pintados-mano-3.jpg'],
                'category_id' => 2,
                'is_active' => true
            ],
            // Copas (category_id: 3)
            [
                'name' => 'Copas Aguardienteras',
                'slug' => 'copas-aguardienteras',
                'description' => 'Set de copas aguardienteras artesanales, perfectas para compartir en reuniones familiares.',
                'price' => 35000,
                'stock' => 12,
                'sku' => 'COP-AGU-001',
                'dimensions' => ['alto' => 8, 'ancho' => 6, 'profundidad' => 6],
                'main_image' => 'products/copas-aguardienteras.jpg',
                'gallery' => ['products/copas-aguardienteras-2.jpg', 'products/copas-aguardienteras-3.jpg'],
                'category_id' => 3,
                'is_active' => true
            ],
            // Juegos (category_id: 4)
            [
                'name' => 'Mini Juego de Rana',
                'slug' => 'mini-juego-rana',
                'description' => 'Tradicional juego de rana en versión miniatura, ideal para entretenimiento familiar.',
                'price' => 55000,
                'stock' => 6,
                'sku' => 'JUE-RAN-001',
                'dimensions' => ['alto' => 20, 'ancho' => 30, 'profundidad' => 25],
                'main_image' => 'products/mini-juego-rana.jpg',
                'gallery' => ['products/mini-juego-rana-2.jpg'],
                'category_id' => 4,
                'is_active' => true
            ],
            // Pinturas (category_id: 5)
            [
                'name' => 'Pintura de Guacamaya en Camisa',
                'slug' => 'pintura-guacamaya-camisa',
                'description' => 'Hermosa pintura artesanal de guacamaya sobre camisa, técnica tradicional.',
                'price' => 75000,
                'stock' => 4,
                'sku' => 'PIN-GUA-001',
                'dimensions' => ['alto' => 40, 'ancho' => 30, 'profundidad' => 1],
                'main_image' => 'products/pintura-guacamaya-camisa.jpg',
                'gallery' => ['products/pintura-guacamaya-camisa-2.jpg'],
                'category_id' => 5,
                'is_active' => true
            ],
            // Manillas (category_id: 6)
            [
                'name' => 'Manillas en Escama de Pescado',
                'slug' => 'manillas-escama-pescado',
                'description' => 'Manillas artesanales elaboradas con escamas de pescado, diseño único y natural.',
                'price' => 18000,
                'stock' => 20,
                'sku' => 'MAN-ESC-001',
                'dimensions' => ['alto' => 1, 'ancho' => 20, 'profundidad' => 1],
                'main_image' => 'products/manillas-escama-pescado.jpg',
                'gallery' => [],
                'category_id' => 6,
                'is_active' => true
            ],
            // Artesanías en Madera (category_id: 7)
            [
                'name' => 'Artesanía en Madera',
                'slug' => 'artesania-madera',
                'description' => 'Pieza única tallada en madera con motivos tradicionales colombianos.',
                'price' => 85000,
                'stock' => 3,
                'sku' => 'MAD-ART-001',
                'dimensions' => ['alto' => 25, 'ancho' => 20, 'profundidad' => 15],
                'main_image' => 'products/artes-madera.jpg',
                'gallery' => [],
                'category_id' => 7,
                'is_active' => true
            ],
            // Agendas (category_id: 8)
            [
                'name' => 'Agenda Ecológica Personalizada',
                'slug' => 'agenda-ecologica-personalizada',
                'description' => 'Agenda ecológica con portada personalizable, papel reciclado y diseño artesanal.',
                'price' => 32000,
                'stock' => 25,
                'sku' => 'AGE-ECO-001',
                'dimensions' => ['alto' => 21, 'ancho' => 15, 'profundidad' => 2],
                'main_image' => 'products/agendas-ecologicas-personalizadas.jpg',
                'gallery' => [],
                'category_id' => 8,
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}