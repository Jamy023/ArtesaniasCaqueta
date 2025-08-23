<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
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
            
            // Eliminar imagen principal de Cloudinary
            if ($product->main_image) {
                $this->deleteCloudinaryImage($product->main_image);
            }
            
            // Eliminar imágenes de galería de Cloudinary
            if ($product->gallery && is_array($product->gallery)) {
                foreach ($product->gallery as $imageUrl) {
                    $this->deleteCloudinaryImage($imageUrl);
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
     * Sube una imagen de producto a Cloudinary
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);
            
            $image = $request->file('image');
            
            // Subir a Cloudinary
            $result = $this->cloudinaryService->uploadImage(
                $image->getRealPath(),
                'products',
                [
                    'public_id' => 'product_' . time() . '_' . Str::random(10),
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]
            );
            
            if (!$result['success']) {
                return response()->json([
                    'message' => 'Error al subir la imagen: ' . $result['error']
                ], 500);
            }
            
            return response()->json([
                'message' => 'Imagen subida exitosamente',
                'url' => $result['url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'],
                'dimensions' => [
                    'width' => $result['width'],
                    'height' => $result['height']
                ],
                'size' => $result['bytes']
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
     * Elimina una imagen específica de Cloudinary
     */
    public function deleteImage(Request $request)
    {
        try {
            $request->validate([
                'image_url' => 'required|string'
            ]);
            
            $imageUrl = $request->image_url;
            
            // Verificar si es una URL de Cloudinary
            if (strpos($imageUrl, 'cloudinary.com') === false) {
                return response()->json([
                    'message' => 'La URL proporcionada no es de Cloudinary'
                ], 400);
            }
            
            // Extraer public_id y eliminar
            $publicId = $this->cloudinaryService->extractPublicIdFromUrl($imageUrl);
            
            if (!$publicId) {
                return response()->json([
                    'message' => 'No se pudo extraer el ID público de la imagen'
                ], 400);
            }
            
            $result = $this->cloudinaryService->deleteImage($publicId);
            
            if ($result['success']) {
                return response()->json([
                    'message' => 'Imagen eliminada exitosamente',
                    'public_id' => $publicId
                ]);
            } else {
                return response()->json([
                    'message' => 'Error al eliminar la imagen: ' . $result['error']
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
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
     * Elimina una imagen de Cloudinary si existe
     */
    private function deleteCloudinaryImage($imageUrl)
    {
        if (!$imageUrl) return;
        
        // Si es una URL de Cloudinary, extraer public_id y eliminar
        if (strpos($imageUrl, 'cloudinary.com') !== false) {
            $publicId = $this->cloudinaryService->extractPublicIdFromUrl($imageUrl);
            if ($publicId) {
                $this->cloudinaryService->deleteImage($publicId);
            }
        } else {
            // Compatibilidad hacia atrás: eliminar del storage local si no es de Cloudinary
            if (Storage::disk('public')->exists($imageUrl)) {
                Storage::disk('public')->delete($imageUrl);
            }
        }
    }
}