<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\PaypalService;
use App\Models\Payment;
use App\Models\User;
use App\Models\Plan;
use App\Http\Controllers\Controller;

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
        $planId = $request->input('plan_id');
        $user = User::find($request->input('user_id'));

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        $plan = Plan::find($planId);

        if (!$plan) {
            return response()->json(['error' => 'Plan no encontrado.'], 404);
        }

        $amount = $plan->price;

        // Crear la orden de PayPal
        $order = $this->paypal->createOrder($amount);

        // Obtener el último transaction_id utilizado
        $lastTransaction = Payment::orderBy('transaction_id', 'desc')->first();
        $newTransactionId = $lastTransaction ? $lastTransaction->transaction_id + 1 : 1; // Iniciar en 1 si no hay registros

        // Crear el pago con el nuevo transaction_id
        $payment = Payment::create([
            'user_id'         => $user->id,
            'plan_id'         => $planId,
            'amount'          => $amount,
            'status'          => Payment::STATUS_PENDING, // ✅ Usamos constante
            'paypal_order_id' => $order['id'],
            'payment_method'  => 'paypal',
            'transaction_id'  => $newTransactionId,  // Usar el nuevo transaction_id incrementado
        ]);

        // Obtener la URL de aprobación de PayPal
        $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'];

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

        $capture = $this->paypal->captureOrder($orderId);

        $payment = Payment::where('paypal_order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['error' => 'Pago no encontrado.'], 404);
        }

        $payment->update([
            'status'             => Payment::STATUS_COMPLETED, // ✅
            'paypal_capture_id'  => $capture['id'],
        ]);

        $payment->user->update([
            'has_access' => true,
        ]);

        return response()->json(['message' => 'Pago exitoso.']);
    }

    /**
     * Maneja cancelación de pago.
     */
    public function cancel()
    {
        return response()->json(['message' => 'El pago fue cancelado.']);
    }
}
