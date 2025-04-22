<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\PaypalService;
use App\Models\Payment;
use App\Models\User;
use App\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class PaymentController extends Controller
{
    protected PayPalService $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    /**
     * Crear una orden de pago en PayPal.
     */
    public function create(Request $request)
    {
        // Validación para asegurarse de que los parámetros existen
        $request->validate([
            'user_id' => 'required|integer',
            'plan_id' => 'required|integer',
        ]);
    
        // Obtener el usuario y plan
        $user = User::find($request->input('user_id'));
        $planId = $request->input('plan_id');
    
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }
    
        $plan = Plan::find($planId);
    
        if (!$plan) {
            return response()->json(['error' => 'Plan no encontrado.'], 404);
        }
    
        // Continuar con el proceso de pago
        $amount = $plan->price;
    
        // Crear la orden de PayPal
        $order = $this->paypal->createOrder($amount);
        
    
        // Obtener el último transaction_id utilizado
        $lastTransaction = Payment::orderBy('transaction_id', 'desc')->first();
        $newTransactionId = $lastTransaction ? $lastTransaction->transaction_id + 1 : 1; // Iniciar en 1 si no hay registros
    
        // Fecha actual (starts_at)
        $now = now();
    
        // Fecha de expiración (ends_at), un mes después
        $endsAt = $now->copy()->addMonth();
    
        // Crear el pago con el nuevo transaction_id
        $payment = Payment::create([
            'user_id'         => $user->id,
            'plan_id'         => $planId,
            'amount'          => $amount,
            'status'          => Payment::STATUS_PENDING, // ✅ Usamos constante
            'paypal_order_id' => $order['id'],
            'payment_method'  => 'paypal',
            'transaction_id'  => $newTransactionId,  // Usar el nuevo transaction_id incrementado
            'starts_at'       => $now,
            'ends_at'         => $endsAt,
        ]);
    
        // Obtener la URL de aprobación de PayPal
        $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'];
    
        \Log::info('Paypal approval URL: ', ['approve_url' => $approveUrl]);

        return response()->json([
            'redirect_url' => $approveUrl,
        ]);
    }

    /**
     * Captura el pago después de ser aprobado por el usuario.
     */
    public function success(Request $request)
    {
        $orderId = $request->query('token');
    
        try {
            // Captura el pago desde PayPal
            $capture = $this->paypal->captureOrder($orderId);
    
            // Registrar la respuesta de PayPal para depuración
            \Log::info('Capture Response:', $capture);
    
            // Validar respuesta de captura
            if (!isset($capture['status']) || $capture['status'] !== 'COMPLETED') {
                return response()->json(['error' => 'La captura del pago no fue completada.'], 400);
            }
    
            // Buscar el pago en nuestra base de datos
            $payment = Payment::where('paypal_order_id', $orderId)->first();
    
            if (!$payment) {
                return response()->json(['error' => 'Pago no encontrado.'], 404);
            }
    
            // Actualizar el pago como completado
            $payment->update([
                'status'             => Payment::STATUS_COMPLETED,
                'paypal_capture_id'  => $capture['id'],
            ]);
    
            $user = $payment->user;

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }

            $user->update(['has_access' => true]);
    
            return redirect()->to("http://localhost:3000/thanks?status=success&user={$payment->user_id}");
        } catch (\Exception $e) {
            // Registrar detalles del error para depuración
            \Log::error('Error al capturar el pago:', ['message' => $e->getMessage()]);
    
            return response()->json([
                'error'   => 'Error al capturar el pago.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    


    /**
     * Maneja cancelación de pago.
     */
    public function cancel()
    {
        return response()->json(['message' => 'El pago fue cancelado.']);
    }


    public function show($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                return response()->json(['error' => 'ID de usuario inválido.'], 400);
            }
    
            // Obtenemos los pagos con la información agregada
            $payments = Payment::where('user_id', $id)
                ->selectRaw('id, user_id, plan_id, transaction_id, amount, status, payment_method, 
                            paypal_order_id, created_at, updated_at, 
                            min(created_at) as starts_at, max(created_at) as ends_at')
                ->groupBy('id', 'user_id', 'plan_id', 'transaction_id', 'amount', 'status', 
                          'payment_method', 'paypal_order_id', 'created_at', 'updated_at', DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
                ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), 'desc')
                ->limit(12)
                ->get();
    
            // Crear una estructura para la respuesta con los datos solicitados
            $response = $payments->map(function ($payment) {
                // Convertir las fechas en instancias de Carbon
                $startsAt = \Carbon\Carbon::parse($payment->starts_at);
                $endsAt = \Carbon\Carbon::parse($payment->ends_at);
    
                return [
                    'id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'plan_id' => $payment->plan_id,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => (float) $payment->amount,
                    'status' => $payment->status,
                    'payment_method' => $payment->payment_method,
                    'paypal_order_id' => $payment->paypal_order_id,
                    'created_at' => $payment->created_at->toIso8601String(),
                    'updated_at' => $payment->updated_at->toIso8601String(),
                    'starts_at' => $startsAt->format('Y-m-d H:i:s'),
                    'ends_at' => $endsAt->format('Y-m-d H:i:s')
                ];
            });
    
            return response()->json(['data' => $response]);
        } catch (\Exception $e) {
            \Log::error('Error al generar estadísticas:', [
                'error' => $e->getMessage(),
                'code'  => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $id
            ]);
    
            return response()->json([
                'error' => 'Error al generar estadísticas.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    

    public function index(Request $request)
    {
        $query = Payment::query();

        // Filtros opcionales si se envían por querystring
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('plan_id')) {
            $query->where('plan_id', $request->input('plan_id'));
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($payments);
    }


}
