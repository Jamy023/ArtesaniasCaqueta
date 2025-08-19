<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController; // 🆕 NUEVO
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===== RUTAS PÚBLICAS =====

// Rutas de productos para clientes (solo productos activos)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']); // Por slug para usuarios

// ===== RUTAS DE GESTIÓN DE CATEGORÍAS

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}/products', [ProductController::class, 'byCategory']);
Route::get('/categories/{id}/show', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::patch('/categories/{id}/toggle-active', [CategoryController::class, 'toggleActive']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::post('/categories/check-slug', [CategoryController::class, 'checkSlug']);

// ===== RUTAS DE GESTIÓN DE PRODUCTOS
Route::get('/products/{id}/show', [ProductController::class, 'showById']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::patch('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::patch('/products/{id}/toggle-active', [ProductController::class, 'toggleActive']);

// Ruta para subir imágenes de productos
Route::post('/upload-product-image', [ProductController::class, 'uploadImage']);

// ===== RUTAS DE AUTENTICACIÓN =====

// Autenticación de clientes (mantiene Sanctum)
Route::post('/clientes/register', [ClienteController::class, 'register']);
Route::post('/clientes/login', [ClienteController::class, 'login']);

// Rutas protegidas de clientes (mantiene Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Perfil del cliente
    Route::get('/clientes/profile', [ClienteController::class, 'profile']);
    Route::put('/clientes/profile', [ClienteController::class, 'updateProfile']);
    Route::post('/clientes/logout', [ClienteController::class, 'logout']);
    
    // 🔥 NUEVAS RUTAS PARA MI CUENTA
    Route::get('/clientes/account/overview', [ClienteController::class, 'accountOverview']);
    Route::post('/clientes/change-password', [ClienteController::class, 'changePassword']);
    Route::delete('/clientes/account', [ClienteController::class, 'deleteAccount']);
    
    // Rutas para direcciones (si las implementas más adelante)
    Route::get('/clientes/addresses', [ClienteController::class, 'getAddresses']);
    Route::post('/clientes/addresses', [ClienteController::class, 'addAddress']);
    Route::put('/clientes/addresses/{id}', [ClienteController::class, 'updateAddress']);
    Route::delete('/clientes/addresses/{id}', [ClienteController::class, 'deleteAddress']);
    
    //  RUTAS DE PEDIDOS PROTEGIDAS
    Route::post('/orders/create', [OrderController::class, 'createOrder']);
    Route::get('/orders', [OrderController::class, 'userOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'showOrder']);
});

//  RUTAS PÚBLICAS DE EPAYCO (sin autenticación)
Route::match(['get', 'post'], '/orders/epayco-response', [OrderController::class, 'epaycoResponse']);
Route::match(['get', 'post'], '/orders/epayco-confirmation', [OrderController::class, 'epaycoConfirmation']);
// ===== RUTAS DE ADMINISTRACIÓN =====

// Autenticación de administradores (SIN prefijo /api)
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/check-auth', [AdminController::class, 'checkAuth']);

// Rutas protegidas de admin (usando middleware personalizado)
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    
    // Logout de admin
    Route::post('/logout', [AdminController::class, 'logout']);
    
    // Dashboard data
    Route::get('/dashboard-stats', [AdminController::class, 'dashboardStats']);
    
    //  RUTAS ADMIN PARA GESTIÓN DE PEDIDOS
    Route::get('/orders', [AdminController::class, 'getAllOrders']);
    Route::get('/orders/{id}', [AdminController::class, 'getOrderDetails']);
    Route::patch('/orders/{id}/status', [AdminController::class, 'updateOrderStatus']);

});

// ===== RUTAS DE GESTIÓN DE USUARIOS =====
Route::get('/users', [AdminController::class, 'getUsers']); 
Route::get('/users/{id}/show', [AdminController::class, 'showUser']);
Route::post('/users', [AdminController::class, 'saveUser']);
Route::put('/users/{id}', [AdminController::class, 'updateUser']);
Route::patch('/users/{id}/toggle-active', [AdminController::class, 'toggleUserStatus']);
Route::delete('/users/{id}', [AdminController::class, 'delete']);


Route::put('/users/{id}/change-password', [AdminController::class, 'changeUserPassword']);




// Ruta simple para servir imágenes via API
Route::get('/image/{filename}', function ($filename) {
    $decodedFilename = urldecode($filename);
    
    // Intentar desde public/storage/products
    $publicPath = public_path('storage/products/' . $decodedFilename);
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }
    
    abort(404, 'Image not found');
});

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando correctamente',
        'timestamp' => now(),
        'laravel_version' => app()->version()
    ]);
});

// Información del usuario autenticado (para clientes)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});