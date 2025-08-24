<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Mostrar el formulario de login del admin
     */
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('app');
    }

    /**
     * Procesar el login del admin
     */
    public function login(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'remember' => 'boolean'
            ]);

            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember', false);

            Log::info('Intento de login admin:', [
                'email' => $credentials['email'],
                'remember' => $remember,
                'ip' => $request->ip()
            ]);

            // Intentar autenticación
            if (Auth::guard('web')->attempt($credentials, $remember)) {
                $user = Auth::guard('web')->user();
                
                Log::info('Login admin exitoso:', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                // Regenerar la sesión para seguridad
                $request->session()->regenerate();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Inicio de sesión exitoso',
                        'redirect' => route('admin.dashboard'),
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ]
                    ]);
                }

                return redirect()->intended(route('admin.dashboard'));
            }

            // Si las credenciales son incorrectas
            Log::warning('Login admin fallido - credenciales incorrectas:', [
                'email' => $credentials['email'],
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las credenciales proporcionadas son incorrectas.',
                    'errors' => [
                        'email' => ['Las credenciales proporcionadas son incorrectas.']
                    ]
                ], 422);
            }

            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas son incorrectas.',
            ]);

        } catch (ValidationException $e) {
            Log::warning('Error de validación en login admin:', [
                'errors' => $e->errors(),
                'email' => $request->email
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;

        } catch (\Exception $e) {
            Log::error('Error inesperado en login admin:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor. Intenta nuevamente.',
                    'errors' => [
                        'general' => ['Error interno del servidor.']
                    ]
                ], 500);
            }

            return back()->withErrors([
                'email' => 'Error interno del servidor. Intenta nuevamente.',
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Verificar el estado de autenticación
     */
    public function checkAuth(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            
            if ($user) {
                return response()->json([
                    'authenticated' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]);
            }

            return response()->json([
                'authenticated' => false,
                'user' => null
            ]);

        } catch (\Exception $e) {
            Log::error('Error en checkAuth:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'authenticated' => false,
                'user' => null,
                'error' => 'Error verificando autenticación'
            ], 500);
        }
    }

    /**
     * Cerrar sesión del admin
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            
            Log::info('Logout admin:', [
                'user_id' => $user ? $user->id : null,
                'email' => $user ? $user->email : null
            ]);

            Auth::guard('web')->logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sesión cerrada exitosamente',
                    'redirect' => route('admin.login')
                ]);
            }

            return redirect()->route('admin.login')
                ->with('success', 'Sesión cerrada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en logout admin:', [
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error cerrando sesión'
                ], 500);
            }

            return redirect()->route('admin.login');
        }
    }

    /**
     * Dashboard del admin
     */
    public function dashboard()
    {
        return view('app'); // O el nombre de tu vista principal de Vue
    }

    /**
     * Página de usuarios
     */
    public function usuarios()
    {
        return view('admin.usuarios');
    }

    /**
     * Página de pedidos
     */
    public function pedidos()
    {
        return view('admin.pedidos');
    }

    /**
     * Crear usuario admin por defecto (para desarrollo)
     * Ejecuta esto una vez: php artisan tinker, luego AdminController::createDefaultAdmin()
     */
    public static function createDefaultAdmin()
    {
        $adminEmail = 'admin@tienda.com';
        
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => $adminEmail,
                'password' => Hash::make('admin123456'),
                'email_verified_at' => now(),
            ]);
            
            echo "Usuario admin creado:\n";
            echo "Email: {$adminEmail}\n";
            echo "Password: admin123456\n";
        } else {
            echo "El usuario admin ya existe.\n";
        }
    }

    /**
     * Crear nuevo usuario
     */
    public function saveUser(Request $request) 
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'is_active' => 'boolean'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->is_active ?? true,
                'email_verified_at' => now() // Auto-verificar usuarios creados por admin
            ]);

            Log::info('Usuario creado por admin:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data' => $user->fresh() // Retornar datos actualizados
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al crear usuario:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el usuario.'
            ], 500);
        }
    }

    /**
     * Obtener lista de usuarios
     */
    public function getUsers() 
    {
        try {
            $usuarios = User::select('id', 'name', 'email', 'created_at', 'is_active')
                          ->orderBy('created_at', 'desc')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $usuarios
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener los usuarios.'
            ], 500);
        }
    }

    /**
     * Mostrar usuario específico
     */
    public function showUser($id) 
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener usuario:', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }
    }

    /**
     * Actualizar usuario
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'is_active' => 'boolean'
            ]);

            $user = User::findOrFail($id);
            
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active
            ]);

            Log::info('Usuario actualizado:', [
                'user_id' => $user->id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => $user->fresh()
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario:', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el usuario.'
            ], 500);
        }
    }

    /**
     * Cambiar estado activo/inactivo del usuario
     */
    public function toggleUserStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevenir que el admin se desactive a sí mismo
            if ($user->id === Auth::id() && $user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes desactivar tu propia cuenta.'
                ], 422);
            }
            
            $user->is_active = !$user->is_active;
            $user->save();

            Log::info('Estado de usuario cambiado:', [
                'user_id' => $user->id,
                'new_status' => $user->is_active,
                'changed_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del usuario:', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo cambiar el estado del usuario.'
            ], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevenir que el admin se elimine a sí mismo
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta.'
                ], 422);
            }

            $userEmail = $user->email; // Para el log
            $user->delete();

            Log::info('Usuario eliminado:', [
                'user_id' => $id,
                'user_email' => $userEmail,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario:', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el usuario.'
            ], 500);
        }
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function changeUserPassword(Request $request, $id) 
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed'
            ]);
            
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();

            Log::info('Contraseña de usuario cambiada:', [
                'user_id' => $user->id,
                'changed_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña:', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la contraseña.'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function dashboardStats()
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $inactiveUsers = User::where('is_active', false)->count();
            $recentUsers = User::where('created_at', '>=', now()->subDays(7))->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'inactive_users' => $inactiveUsers,
                    'recent_users' => $recentUsers,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener las estadísticas.'
            ], 500);
        }
    }

    /**
     * Obtener todos los pedidos (para admin)
     */
    public function getAllOrders(Request $request)
    {
        try {
            $orders = \App\Models\Order::with(['cliente'])
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener pedidos:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener los pedidos.'
            ], 500);
        }
    }

    /**
     * Obtener detalles de un pedido específico
     */
    public function getOrderDetails($id)
    {
        try {
            $order = \App\Models\Order::with(['cliente'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'order' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener detalles del pedido:', [
                'error' => $e->getMessage(),
                'order_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado.'
            ], 404);
        }
    }

    /**
     * Actualizar el estado de un pedido
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,paid,failed,cancelled',
                'notes' => 'nullable|string|max:1000'
            ]);

            $order = \App\Models\Order::findOrFail($id);
            
            $oldStatus = $order->status;
            $order->status = $request->status;
            
            if ($request->has('notes')) {
                $order->notes = $request->notes;
            }
            
            $order->save();

            Log::info('Estado de pedido actualizado por admin:', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $order->status,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del pedido actualizado correctamente',
                'order' => $order
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado del pedido:', [
                'error' => $e->getMessage(),
                'order_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el estado del pedido.'
            ], 500);
        }
    }
}