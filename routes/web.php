<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Redirección de la raíz (opcional)
Route::get('/', function () {
    return view('app'); // o redirigir a tu frontend Vue
});

// Ruta para servir imágenes de storage - DEBE IR ANTES de /{any}
Route::get('/storage/products/{filename}', function ($filename) {
    // Decodificar la URL para manejar espacios y caracteres especiales
    $decodedFilename = urldecode($filename);
    
    // Primero intentar desde public/storage/products donde están las imágenes
    $publicPath = public_path('storage/products/' . $decodedFilename);
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }
    
    // Fallback a storage/app/public/products
    $storagePath = storage_path('app/public/products/' . $decodedFilename);
    if (file_exists($storagePath)) {
        return response()->file($storagePath);
    }
    
    abort(404);
})->where('filename', '.*');

Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
    // Correcta: esta ruta queda como "/admin/login" y se llama "admin.login"
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    Route::get('/check-auth', [AdminController::class, 'checkAuth'])->name('check-auth');

    Route::middleware('auth:web')->group(function () {
         Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');


        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/dashboard-data', [AdminController::class, 'dashboardData'])->name('dashboard-data');
        Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
        Route::get('/pedidos', [AdminController::class, 'pedidos'])->name('pedidos');
        Route::get('/productos', [AdminController::class, 'dashboard'])->name('productos');
    });
});

// Para todas las demás rutas, servir tu aplicación Vue - VA AL FINAL
Route::get('/{any}', function () {
    return view('app'); // Tu vista principal de Vue
})->where('any', '.*'); // Capturar todas las rutas para Vue