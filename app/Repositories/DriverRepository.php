<?php

namespace App\Repositories;

use App\Models\Driver;
use App\Models\DeliveryHistory;

class DriverRepository implements DriverRepositoryInterface
{
    public function all()
    {
        return Driver::all();
    }

    public function find($id)
    {
        return Driver::findOrFail($id);
    }

    public function create(array $data)
    {
        return Driver::create($data);
    }

    public function update($id, array $data)
    {
        $driver = Driver::findOrFail($id);
        $driver->update($data);
        return $driver;
    }

    public function delete($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
    }

    public function getAvailableDrivers()
    {
        return Driver::where('status', 'available')->get();
    }

    public function getDriverHistory($driverId)
    {
        // Asumsi DeliveryHistory memiliki relasi dengan Driver dan Order
        return DeliveryHistory::where('driver_id', $driverId)->with('order')->get();
    }
}