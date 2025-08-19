<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Ruta para servir imágenes de storage
Route::get('/storage/products/{filename}', function ($filename) {
    $path = storage_path('app/public/products/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->where('filename', '.*');

// Redirección de la raíz (opcional)
Route::get('/', function () {
    return view('app'); // o redirigir a tu frontend Vue
});

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

// Para todas las demás rutas, servir tu aplicación Vue
Route::get('/{any}', function () {
    return view('app'); // Tu vista principal de Vue
})->where('any', '.*'); // Capturar todas las rutas para Vue