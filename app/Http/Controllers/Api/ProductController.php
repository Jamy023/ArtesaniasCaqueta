<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Lista productos - Funciona para clientes Y para admin
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Si viene el parámetro admin_view, mostrar todos los productos
        // Si no, solo productos activos (para clientes normales)
        if (!$request->has('admin_view')) {
            $query->where('is_active', true);
        }
        
        // Búsqueda por nombre
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filtro por categoría
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filtro por estado (útil para admin)
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        // Paginación
        $perPage = $request->get('per_page', 12);
        
        return $query->paginate($perPage);
    }

    /**
     * Muestra un producto específico por SLUG (para usuarios públicos)
     */
    public function show($slug)
    {
        // Intentar encontrar por slug primero (para compatibilidad con usuarios)
        $query = Product::where('slug', $slug)->with('category');
        
        // Si no se encuentra por slug, intentar por ID (para admin)
        if (!$query->exists() && is_numeric($slug)) {
            $query = Product::where('id', $slug)->with('category');
        }
        
        // Solo filtrar por activos si NO viene admin_view
        if (!request()->has('admin_view')) {
            $query->where('is_active', true);
        }
        
        $product = $query->first();
        
        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }
        
        return response()->json($product);
    }

    /**
     * Muestra un producto específico por ID (para admin)
     */
    public function showById($id)
    {
        $query = Product::where('id', $id)->with('category');
        
        // Solo filtrar por activos si NO viene admin_view
        if (!request()->has('admin_view')) {
            $query->where('is_active', true);
        }
        
        $product = $query->first();
        
        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }
        
        return response()->json([
            'message' => 'Producto obtenido correctamente',
            'data' => $product
        ]);
    }

    /**
     * Crea un nuevo producto
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'sku' => 'nullable|string|unique:products,sku',
                'dimensions' => 'nullable|array',
                'main_image' => 'nullable|string',
                'gallery' => 'nullable|array',
                'category_id' => 'required|exists:categories,id',
                'is_active' => 'boolean'
            ]);
            
            // Generar slug automáticamente
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
            
            $product = Product::create($validated);
            $product->load('category');
            
            return response()->json([
                'message' => 'Producto creado exitosamente',
                'data' => $product
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualiza un producto
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'sku' => 'nullable|string|unique:products,sku,' . $product->id,
                'dimensions' => 'nullable|array',
                'main_image' => 'nullable|string',
                'gallery' => 'nullable|array',
                'category_id' => 'required|exists:categories,id',
                'is_active' => 'boolean'
            ]);
            
            // Si cambió el nombre, regenerar el slug
            if ($validated['name'] !== $product->name) {
                $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
            }
            
            $product->update($validated);
            $product->load('category');
            
            return response()->json([
                'message' => 'Producto actualizado exitosamente',
                'data' => $product
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Elimina un producto
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Eliminar imágenes asociadas del storage
            if ($product->main_image) {
                $this->deleteImageIfExists($product->main_image);
            }
            
            if ($product->gallery && is_array($product->gallery)) {
                foreach ($product->gallery as $image) {
                    $this->deleteImageIfExists($image);
                }
            }
            
            $product->delete();
            
            return response()->json([
                'message' => 'Producto eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cambia el estado activo/inactivo
     */
    public function toggleActive($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $product->update([
                'is_active' => !$product->is_active
            ]);
            
            $status = $product->is_active ? 'activado' : 'desactivado';
            
            return response()->json([
                'message' => "Producto {$status} exitosamente",
                'data' => $product
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cambiar el estado del producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sube una imagen de producto
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);
            
            $image = $request->file('image');
            
            // Generar nombre único para la imagen
            $fileName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Guardar en storage/app/public/products
            $path = $image->storeAs('products', $fileName, 'public');
            
            return response()->json([
                'message' => 'Imagen subida exitosamente',
                'path' => $path,
                'url' => Storage::url($path)
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir la imagen: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Productos por categoría (método público)
     */
    public function byCategory($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            $query = Product::where('category_id', $categoryId)->with('category');
            
            // Solo productos activos para vista pública
            if (!request()->has('admin_view')) {
                $query->where('is_active', true);
            }
            
            $products = $query->orderBy('created_at', 'desc')->paginate(12);
            
            return response()->json([
                'message' => 'Productos obtenidos correctamente',
                'category' => $category,
                'data' => $products
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener productos de la categoría: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Genera un slug único
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        $query = Product::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        
        return $slug;
    }
    
    /**
     * Elimina una imagen del storage si existe
     */
    private function deleteImageIfExists($imagePath)
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}