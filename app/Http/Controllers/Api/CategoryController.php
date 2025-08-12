<?php

// En el controlador CategoryController, actualiza el método store y update para manejar mejor los slugs únicos

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::withCount('products')->orderBy('created_at', 'desc')->get();
    }

    public function show($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|url|max:500',
            'is_active' => 'boolean'
        ]);

        // Generar slug único
        $baseSlug = Str::slug($request->name);
        $slug = $this->generateUniqueSlug($baseSlug);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'image' => $request->image,
            'is_active' => $request->is_active ?? true
        ]);

        $category->loadCount('products');

        return response()->json([
            'message' => 'Categoría creada correctamente',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id)
            ],
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|url|max:500',
            'is_active' => 'boolean'
        ]);

        // Generar nuevo slug si el nombre cambió
        $slug = $category->slug;
        if ($request->name !== $category->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $this->generateUniqueSlug($baseSlug, $category->id);
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'image' => $request->image,
            'is_active' => $request->is_active ?? $category->is_active
        ]);

        $category->loadCount('products');

        return response()->json([
            'message' => 'Categoría actualizada correctamente',
            'data' => $category
        ]);
    }

    public function toggleActive($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $category->loadCount('products');

        return response()->json([
            'message' => $category->is_active ? 'Categoría activada correctamente' : 'Categoría desactivada correctamente',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Verificar si tiene productos asociados
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ]);
    }

    /**
     * Generar un slug único
     */
    private function generateUniqueSlug($baseSlug, $ignoreId = null)
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verificar si un slug ya existe
     */
    private function slugExists($slug, $ignoreId = null)
    {
        $query = Category::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Método para verificar disponibilidad de slug (opcional para AJAX)
     */
    public function checkSlug(Request $request)
    {
        $request->validate([
            'slug' => 'required|string',
            'ignore_id' => 'nullable|integer'
        ]);

        $exists = $this->slugExists($request->slug, $request->ignore_id);

        return response()->json([
            'available' => !$exists,
            'suggestion' => $exists ? $this->generateUniqueSlug($request->slug, $request->ignore_id) : null
        ]);
    }
}