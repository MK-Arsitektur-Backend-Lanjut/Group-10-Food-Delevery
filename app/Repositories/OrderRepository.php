<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::select(['id', 'user_id', 'restaurant_id', 'driver_id', 'status', 'total_price', 'created_at'])
            ->with([
                'user:id,name',
                'deliveryHistories:id,order_id,driver_id,delivered_at',
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function cursorPaginate(int $perPage = 500)
    {
        return Order::select(['id', 'user_id', 'restaurant_id', 'driver_id', 'status', 'total_price', 'created_at'])
            ->with([
                'user:id,name',
                'deliveryHistories:id,order_id,driver_id,delivered_at',
            ])
            ->orderBy('id')
            ->cursorPaginate($perPage);
    }

    public function create($data)
    {
        return Order::create($data);
    }

    public function findById($id)
    {
        return Order::findOrFail($id);
    }

    public function updateStatus($order, $status)
    {
        $order->status = $status;
        $order->save();

        return $order;
    }

    public function findByIdWithLock($id)
    {
        return Order::with(['user', 'deliveryHistories'])
            ->where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
