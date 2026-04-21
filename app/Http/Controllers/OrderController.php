<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create Order",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="Order created"
     *     )
     * )
     */
    public function store()
    {
        $order = $this->orderRepository->create([
            'user_id' => 1,
            'restaurant_id' => 1,
            'status' => 'DIPESAN',
            'total_price' => 15000
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'DIPESAN'
        ]);

        return response()->json($order);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get Order by ID",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function show($id)
    {
        return response()->json(
            $this->orderRepository->findByIdWithLock($id)
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/orders/{id}/status",
     *     summary="Update Order Status",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="DIMASAK")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated"
     *     )
     * )
     */
    public function updateStatus(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $order = $this->orderRepository->findByIdWithLock($id);

            sleep(5); // simulasi concurrency

            $newStatus = $request->status;

            $allowedStatuses = ['DIPESAN', 'DIMASAK', 'DIANTAR', 'SELESAI'];

            if (!in_array($newStatus, $allowedStatuses)) {
                return response()->json([
                    'message' => 'Status tidak valid'
                ], 400);
            }

            if (!$this->isValidTransition($order->status, $newStatus)) {
                return response()->json([
                    'message' => 'Transisi status tidak valid'
                ], 400);
            }

            $order = $this->orderRepository->updateStatus($order, $newStatus);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $newStatus
            ]);

            return response()->json($order);
        });
    }

    private function isValidTransition($current, $next)
    {
        $rules = [
            'DIPESAN' => ['DIMASAK'],
            'DIMASAK' => ['DIANTAR'],
            'DIANTAR' => ['SELESAI'],
            'SELESAI' => []
        ];

        return in_array($next, $rules[$current] ?? []);
    }
}