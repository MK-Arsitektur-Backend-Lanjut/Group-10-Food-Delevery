<?php

namespace App\Http\Controllers;

use App\Models\OrderStatusLog;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    #[OA\Post(
        path: '/api/orders',
        summary: 'Create Order',
        tags: ['Orders'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreOrderRequest')
        ),
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Order created', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
        ]
    )]
    public function store()
    {
        $order = $this->orderRepository->create([
            'user_id' => 1,
            'restaurant_id' => 1,
            'status' => 'DIPESAN',
            'total_price' => 15000,
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'DIPESAN',
        ]);

        return response()->json($order);
    }

    #[OA\Get(
        path: '/api/orders/{id}',
        summary: 'Get Order by ID',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
        ]
    )]
    public function show($id)
    {
        return response()->json(
            $this->orderRepository->findByIdWithLock($id)
        );
    }

    #[OA\Patch(
        path: '/api/orders/{id}/status',
        summary: 'Update Order Status',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderStatusRequest')
        ),
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 400, description: 'Status tidak valid atau transisi tidak valid'),
        ]
    )]
    public function updateStatus(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $order = $this->orderRepository->findByIdWithLock($id);

            sleep(5); // simulasi concurrency

            $newStatus = $request->status;

            $allowedStatuses = ['DIPESAN', 'DIMASAK', 'DIANTAR', 'SELESAI'];

            if (! in_array($newStatus, $allowedStatuses)) {
                return response()->json([
                    'message' => 'Status tidak valid',
                ], 400);
            }

            if (! $this->isValidTransition($order->status, $newStatus)) {
                return response()->json([
                    'message' => 'Transisi status tidak valid',
                ], 400);
            }

            $order = $this->orderRepository->updateStatus($order, $newStatus);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $newStatus,
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
            'SELESAI' => [],
        ];

        return in_array($next, $rules[$current] ?? []);
    }
}
