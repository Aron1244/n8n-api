<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Pagos",
 *     description="Operaciones relacionadas con los pagos a través de PayPal"
 * )
 */
class PaymentDocs
{
    /**
     * @OA\Post(
     *     path="/payments/create",
     *     summary="Crear una orden de pago con PayPal",
     *     tags={"Pagos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "plan_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="plan_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL de aprobación de PayPal",
     *         @OA\JsonContent(
     *             @OA\Property(property="redirect_url", type="string", example="https://paypal.com/checkoutnow?token=...")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Usuario no autenticado"),
     *     @OA\Response(response=404, description="Plan no encontrado"),
     *     @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function create() {}

    /**
     * @OA\Get(
     *     path="/payments/success",
     *     summary="Captura el pago aprobado en PayPal",
     *     tags={"Pagos"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="ID de la orden generada por PayPal"
     *     ),
     *     @OA\Response(response=302, description="Redirección al cliente tras éxito"),
     *     @OA\Response(response=400, description="Captura no completada"),
     *     @OA\Response(response=404, description="Pago no encontrado"),
     *     @OA\Response(response=500, description="Error al capturar el pago")
     * )
     */
    public function success() {}

    /**
     * @OA\Get(
     *     path="/payments/cancel",
     *     summary="Cancelar el flujo de pago",
     *     tags={"Pagos"},
     *     @OA\Response(
     *         response=200,
     *         description="Pago cancelado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El pago fue cancelado.")
     *         )
     *     )
     * )
     */
    public function cancel() {}

    /**
     * @OA\Get(
     *     path="/payments/{id}",
     *     summary="Mostrar los últimos pagos de un usuario",
     *     tags={"Pagos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pagos del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="transaction_id", type="string"),
     *                 @OA\Property(property="amount", type="number", format="float"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="starts_at", type="string"),
     *                 @OA\Property(property="ends_at", type="string")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=400, description="ID inválido"),
     *     @OA\Response(response=500, description="Error interno")
     * )
     */
    public function show() {}

    /**
     * @OA\Get(
     *     path="/payments",
     *     summary="Lista paginada de todos los pagos",
     *     tags={"Pagos"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filtrar por estado del pago"
     *     ),
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filtrar por plan"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de pagos",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="amount", type="number", format="float")
     *             ))
     *         )
     *     )
     * )
     */
    public function index() {}
}
