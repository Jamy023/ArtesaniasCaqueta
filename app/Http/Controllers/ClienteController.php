<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClienteController extends Controller
{
    public function register(Request $request)
    {
        try {
            Log::info('Intento de registro', $request->all());
            
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'tipo_documento' => 'required|in:CC,TI,CE,PP,NIT',
                'numero_documento' => 'required|string|unique:clientes,numero_documento',
                'email' => 'required|string|email|max:255|unique:clientes',
                'password' => 'required|string|min:8|confirmed',
                'telefono' => 'nullable|string|max:15',
                'fecha_nacimiento' => 'nullable|date',
                'direccion' => 'nullable|string|max:255',
                'ciudad' => 'nullable|string|max:100',
                'departamento' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en registro', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'departamento' => $request->departamento,
            ]);

            // Crear token usando el modelo Cliente
            $token = $cliente->createToken('auth_token')->plainTextToken;

            Log::info('Registro exitoso', ['cliente_id' => $cliente->id]);

            return response()->json([
                'cliente' => $cliente,
                'token' => $token,
                'message' => 'Cliente registrado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error interno del servidor',
                'errors' => ['general' => ['Ocurrió un error inesperado: ' . $e->getMessage()]]
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            Log::info('Intento de login', ['email' => $request->email]);
            
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en login', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cliente = Cliente::where('email', $request->email)->first();

            if (!$cliente) {
                Log::warning('Cliente no encontrado', ['email' => $request->email]);
                return response()->json([
                    'message' => 'Credenciales incorrectas',
                    'errors' => ['email' => ['Las credenciales proporcionadas son incorrectas.']]
                ], 401);
            }

            if (!Hash::check($request->password, $cliente->password)) {
                Log::warning('Contraseña incorrecta', ['email' => $request->email]);
                return response()->json([
                    'message' => 'Credenciales incorrectas',
                    'errors' => ['password' => ['Las credenciales proporcionadas son incorrectas.']]
                ], 401);
            }

            // Eliminar tokens anteriores (opcional)
            $cliente->tokens()->delete();

            $token = $cliente->createToken('auth_token')->plainTextToken;

            Log::info('Login exitoso', ['cliente_id' => $cliente->id]);

            return response()->json([
                'cliente' => $cliente,
                'token' => $token,
                'message' => 'Inicio de sesión exitoso'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error interno del servidor',
                'errors' => ['general' => ['Ocurrió un error inesperado: ' . $e->getMessage()]]
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            Log::info('Logout exitoso', ['cliente_id' => $request->user()->id]);
            
            return response()->json(['message' => 'Sesión cerrada exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error en logout: ' . $e->getMessage());
            return response()->json(['message' => 'Error al cerrar sesión'], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            return response()->json([
                'cliente' => $request->user(),
                'message' => 'Perfil obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo perfil: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo perfil'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'telefono' => 'nullable|string|max:15',
                'direccion' => 'nullable|string|max:255',
                'ciudad' => 'nullable|string|max:100',
                'departamento' => 'nullable|string|max:100',
                'fecha_nacimiento' => 'nullable|date',
                'email' => 'sometimes|required|string|email|max:255|unique:clientes,email,' . $request->user()->id,
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cliente = $request->user();
            $cliente->update($request->only([
                'nombre', 'apellido', 'telefono', 'direccion', 
                'ciudad', 'departamento', 'fecha_nacimiento', 'email'
            ]));

            Log::info('Perfil actualizado', ['cliente_id' => $cliente->id]);

            return response()->json([
                'cliente' => $cliente->fresh(),
                'message' => 'Perfil actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando perfil: ' . $e->getMessage());
            return response()->json(['message' => 'Error actualizando perfil'], 500);
        }
    }

    // 🔥 NUEVO: Resumen de la cuenta
    public function accountOverview(Request $request)
    {
        try {
            $cliente = $request->user();
            
            // Datos básicos del cliente
            $accountData = [
                'cliente' => $cliente,
                'cuenta_creada' => $cliente->created_at->format('d/m/Y'),
                'tiempo_registrado' => $cliente->created_at->diffForHumans(),
                'ultimo_acceso' => $cliente->updated_at->format('d/m/Y H:i'),
                
                // Estadísticas que puedes implementar más adelante
                'total_pedidos' => 0, // $cliente->orders()->count(),
                'pedidos_completados' => 0, // $cliente->orders()->where('status', 'completed')->count(),
                'monto_total_gastado' => 0, // $cliente->orders()->where('status', 'completed')->sum('total'),
                'producto_favorito' => null, // Puedes implementar esto más adelante
                
                // Estado de la cuenta
                'estado_cuenta' => 'Activa',
                'verificacion_email' => $cliente->email_verified_at ? 'Verificado' : 'Pendiente',
            ];

            return response()->json([
                'data' => $accountData,
                'message' => 'Resumen de cuenta obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo resumen de cuenta: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo resumen de cuenta'], 500);
        }
    }

    // 🔥 NUEVO: Cambiar contraseña
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cliente = $request->user();

            // Verificar contraseña actual
            if (!Hash::check($request->current_password, $cliente->password)) {
                return response()->json([
                    'errors' => ['current_password' => ['La contraseña actual es incorrecta']]
                ], 422);
            }

            // Actualizar contraseña
            $cliente->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Revocar todos los tokens existentes por seguridad
            $cliente->tokens()->delete();

            Log::info('Contraseña cambiada', ['cliente_id' => $cliente->id]);

            return response()->json([
                'message' => 'Contraseña actualizada exitosamente. Por seguridad, debes iniciar sesión nuevamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cambiando contraseña: ' . $e->getMessage());
            return response()->json(['message' => 'Error al cambiar contraseña'], 500);
        }
    }

    // 🔥 NUEVO: Eliminar cuenta
    public function deleteAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'confirmation' => 'required|in:ELIMINAR'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cliente = $request->user();

            // Verificar contraseña
            if (!Hash::check($request->password, $cliente->password)) {
                return response()->json([
                    'errors' => ['password' => ['La contraseña es incorrecta']]
                ], 422);
            }

            // Eliminar todos los tokens
            $cliente->tokens()->delete();

            // En lugar de eliminar completamente, podrías marcar como inactivo
            // $cliente->update(['active' => false, 'deleted_at' => now()]);
            
            // O eliminar completamente (cuidado con relaciones)
            $clienteId = $cliente->id;
            $cliente->delete();

            Log::info('Cuenta eliminada', ['cliente_id' => $clienteId]);

            return response()->json([
                'message' => 'Cuenta eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando cuenta: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar cuenta'], 500);
        }
    }

    // 🔥 NUEVO: Obtener direcciones (placeholder para futuro)
    public function getAddresses(Request $request)
    {
        try {
            $cliente = $request->user();
            
            // Por ahora devolvemos la dirección principal del perfil
            $addresses = [];
            
            if ($cliente->direccion) {
                $addresses[] = [
                    'id' => 1,
                    'tipo' => 'Principal',
                    'direccion' => $cliente->direccion,
                    'ciudad' => $cliente->ciudad,
                    'departamento' => $cliente->departamento,
                    'es_principal' => true
                ];
            }

            return response()->json([
                'addresses' => $addresses,
                'message' => 'Direcciones obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo direcciones: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo direcciones'], 500);
        }
    }
}