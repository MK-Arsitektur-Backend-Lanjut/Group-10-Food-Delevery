<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
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
    return \App\Models\Order::where('id', $id)
        ->lockForUpdate()
        ->firstOrFail();
    }
}