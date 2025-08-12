<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sku',
        'dimensions',
        'main_image',
        'gallery',
        'is_active',
        'category_id'
    ];

    // Convertir JSON a array automáticamente
    protected $casts = [
        'dimensions' => 'array',
        'gallery' => 'array',
        'is_active' => 'boolean'
    ];

    // Relación: Un producto pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}