<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Abanicos',
                'slug' => 'abanicos',
                'description' => 'Abanicos artesanales en diferentes diseños y materiales',
                'is_active' => true
            ],
            [
                'name' => 'Bolsos',
                'slug' => 'bolsos',
                'description' => 'Bolsos en yute pintados a mano con diseños únicos',
                'is_active' => true
            ],
            [
                'name' => 'Copas y Recipientes',
                'slug' => 'copas-recipientes',
                'description' => 'Copas aguardienteras y recipientes artesanales',
                'is_active' => true
            ],
            [
                'name' => 'Juegos y Entretenimiento',
                'slug' => 'juegos-entretenimiento',
                'description' => 'Mini juegos artesanales y productos de entretenimiento',
                'is_active' => true
            ],
            [
                'name' => 'Pinturas y Arte',
                'slug' => 'pinturas-arte',
                'description' => 'Pinturas artesanales y obras de arte',
                'is_active' => true
            ],
            [
                'name' => 'Manillas y Accesorios',
                'slug' => 'manillas-accesorios',
                'description' => 'Manillas y accesorios artesanales',
                'is_active' => true
            ],
            [
                'name' => 'Artesanías en Madera',
                'slug' => 'artesanias-madera',
                'description' => 'Productos artesanales elaborados en madera',
                'is_active' => true
            ],
            [
                'name' => 'Agendas y Papelería',
                'slug' => 'agendas-papeleria',
                'description' => 'Agendas personalizadas y productos de papelería',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}