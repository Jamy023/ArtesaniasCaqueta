<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Crear un nuevo pedido y generar datos para ePayco
     */
    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'total_amount' => 'required|numeric|min:0',
            ]);

            /** @var Cliente $cliente */
            $cliente = Auth::guard('sanctum')->user();
            
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Crear el pedido
            $order = Order::create([
                'cliente_id' => $cliente->id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $request->total_amount,
                'status' => Order::STATUS_PENDING,
                'items' => $request->items,
                'customer_data' => [
                    'nombre' => $cliente->nombre,
                    'apellido' => $cliente->apellido,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'documento' => $cliente->numero_documento,
                    'tipo_documento' => $cliente->tipo_documento,
                    'direccion' => $cliente->direccion,
                    'ciudad' => $cliente->ciudad,
                    'departamento' => $cliente->departamento,
                ]
            ]);

            // Generar datos para ePayco
            $epaycoData = $this->generateEpaycoData($order, $cliente);

            // Log para debugging
            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'epayco_data_keys' => array_keys($epaycoData)
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'epayco_data' => $epaycoData
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido'
            ], 500);
        }
    }

    /**
     * Generar datos para ePayco
     */
    private function generateEpaycoData(Order $order, Cliente $cliente)
{
    // ✅ CONFIGURACIÓN CORRECTA CON TUS DATOS
    $config = [
        // Para formulario de checkout
        'p_cust_id_cliente' => env('EPAYCO_CUSTOMER_ID', '1556492'),
        'p_key' => env('EPAYCO_PUBLIC_KEY'), // Tu P_KEY real (sin asteriscos)
    ];

    // 🔥 VALIDACIÓN MEJORADA
    if (empty($config['p_key'])) {
        Log::error('ePayco P_KEY not configured in .env file');
        throw new \Exception('Error de configuración: P_KEY de ePayco no encontrada');
    }

    $signature = $this->generateSignature($order, $config);

    return [
        // Datos obligatorios de ePayco
        'p_cust_id_cliente' => $config['p_cust_id_cliente'], // 1556492
        'p_key' => $config['p_key'], // Tu P_KEY real
        'p_id_invoice' => $order->order_number,
        'p_description' => 'Pedido #' . $order->order_number . ' - Artesanías',
        'p_amount' => (string) round($order->total_amount, 0),
        'p_amount_base' => (string) round($order->total_amount, 0),
        'p_currency_code' => 'COP',
        'p_signature' => $signature,
        
        // Datos del cliente
        'p_email' => $cliente->email,
        'p_billing_customer' => trim($cliente->nombre . ' ' . $cliente->apellido),
        'p_customer_name' => $cliente->nombre,
        'p_customer_lastname' => $cliente->apellido,
        'p_customer_email' => $cliente->email,
        'p_customer_phone' => $cliente->telefono ?? '',
        'p_customer_doc_type' => $this->mapDocumentType($cliente->tipo_documento),
        'p_customer_document' => $cliente->numero_documento,
        'p_customer_address' => $cliente->direccion ?? 'No especificada',
        'p_customer_city' => $cliente->ciudad ?? 'Bogotá',
        'p_customer_country' => 'CO',
        
        // URLs de respuesta - usar URL base específica de Railway
        'p_url_response' => 'https://artesaniascaqueta-production.up.railway.app/api/orders/epayco-response',
        'p_url_confirmation' => 'https://artesaniascaqueta-production.up.railway.app/api/orders/epayco-confirmation',
        
        // Configuración
        'p_test_request' => env('EPAYCO_TEST_MODE', 'TRUE'),
        'p_method_confirmation' => 'POST',
    ];
}
    /**
     * Generar firma para ePayco
     */
    private function generateSignature(Order $order, array $config)
    {
        $amount = (string) round($order->total_amount, 0);
        
        $signature_string = $config['p_cust_id_cliente'] . '^' . 
                           $config['p_key'] . '^' . 
                           $order->order_number . '^' . 
                           $amount . '^' . 
                           'COP';

        return md5($signature_string);
    }

    /**
     * Mapear tipo de documento para ePayco
     */
    private function mapDocumentType($tipo)
    {
        $mapping = [
            'CC' => 'CC',
            'CE' => 'CE',
            'TI' => 'TI',
            'PP' => 'PP',
            'NIT' => 'NIT',
        ];

        return $mapping[$tipo] ?? 'CC';
    }

    /**
     * Página de respuesta de ePayco (donde llega el usuario)
     */
    public function epaycoResponse(Request $request)
    {
        try {
            $ref_payco = $request->input('ref_payco');
            $order_number = $request->input('p_id_invoice');
            $estado = $request->input('x_cod_response');

            Log::info('ePayco Response received', $request->all());

            // Buscar el pedido
            $order = Order::where('order_number', $order_number)->first();

            if (!$order) {
                return redirect('/')->with('error', 'Pedido no encontrado');
            }

            // Actualizar el pedido con la referencia de ePayco
            $order->update([
                'epayco_ref' => $ref_payco,
                'epayco_transaction_id' => $request->input('x_transaction_id'),
            ]);

            // Redirigir según el estado a nuestra página de confirmación
            $baseParams = [
                'order' => $order->order_number,
                'transaction_id' => $request->input('x_transaction_id'),
                'amount' => $order->total_amount
            ];

            switch ($estado) {
                case '1': // Transacción aprobada
                    $params = array_merge($baseParams, ['success' => 'true']);
                    return redirect('/order-confirmation?' . http_build_query($params));
                    
                case '2': // Transacción rechazada
                    $params = array_merge($baseParams, ['error' => 'rejected']);
                    return redirect('/order-confirmation?' . http_build_query($params));
                    
                case '3': // Transacción pendiente
                    $params = array_merge($baseParams, ['pending' => 'true']);
                    return redirect('/order-confirmation?' . http_build_query($params));
                    
                default:
                    $params = array_merge($baseParams, ['error' => 'unknown']);
                    return redirect('/order-confirmation?' . http_build_query($params));
            }

        } catch (\Exception $e) {
            Log::error('Error in ePayco response: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error procesando el pago');
        }
    }

    /**
     * Confirmación de ePayco (webhook)
     */
    public function epaycoConfirmation(Request $request)
    {
        try {
            Log::info('ePayco Confirmation received', $request->all());

            $ref_payco = $request->input('x_ref_payco');
            $order_number = $request->input('x_id_invoice');
            $estado = $request->input('x_cod_response');
            $amount = $request->input('x_amount');

            // Buscar el pedido
            $order = Order::where('order_number', $order_number)->first();

            if (!$order) {
                Log::error('Order not found: ' . $order_number);
                return response('Order not found', 404);
            }

            // Verificar el monto
            if (abs($order->total_amount - floatval($amount)) > 0.01) {
                Log::error('Amount mismatch for order: ' . $order_number);
                return response('Amount mismatch', 400);
            }

            // Actualizar estado del pedido según respuesta de ePayco
            switch ($estado) {
                case '1': // Transacción aprobada
                    $order->update([
                        'status' => Order::STATUS_PAID,
                        'epayco_ref' => $ref_payco,
                        'epayco_transaction_id' => $request->input('x_transaction_id'),
                        'payment_method' => $request->input('x_franchise'),
                    ]);
                    break;
                
                case '2': // Transacción rechazada
                    $order->update([
                        'status' => Order::STATUS_FAILED,
                        'epayco_ref' => $ref_payco,
                    ]);
                    break;
                
                case '3': // Transacción pendiente
                    // Mantener como pending
                    $order->update([
                        'epayco_ref' => $ref_payco,
                    ]);
                    break;
                
                default:
                    Log::warning('Unknown status code: ' . $estado . ' for order: ' . $order_number);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Error in ePayco confirmation: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Obtener pedidos del usuario autenticado
     */
    public function userOrders(Request $request)
    {
        try {
            /** @var Cliente $cliente */
            $cliente = Auth::guard('sanctum')->user();

            $orders = Order::where('cliente_id', $cliente->id)
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user orders: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pedidos'
            ], 500);
        }
    }

    /**
     * Obtener un pedido específico
     */
    public function showOrder(Request $request, $id)
    {
        try {
            /** @var Cliente $cliente */
            $cliente = Auth::guard('sanctum')->user();

            $order = Order::where('cliente_id', $cliente->id)
                         ->where('id', $id)
                         ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'order' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el pedido'
            ], 500);
        }
    }
}