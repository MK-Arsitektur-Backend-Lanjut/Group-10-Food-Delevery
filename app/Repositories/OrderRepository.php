<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function paginate($perPage = 15)
    {
        return Order::with(['user', 'deliveryHistories'])->latest()->paginate($perPage);
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
        return \App\Models\Order::with(['user', 'deliveryHistories'])
            ->where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();
    }
}