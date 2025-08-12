<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'name',
        'slug', 
        'description',
        'image',
        'is_active'
    ];

    // Relación: Una categoría tiene muchos productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}