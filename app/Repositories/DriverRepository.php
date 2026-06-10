<?php

namespace App\Repositories;

use App\Models\DeliveryHistory;
use App\Models\Driver;

class DriverRepository implements DriverRepositoryInterface
{
    public function all(int $perPage = 15)
    {
        return Driver::paginate($perPage);
    }

    public function cursorPaginate(int $perPage = 500)
    {
        return Driver::orderBy('id')->cursorPaginate($perPage);
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

    public function getAvailableDrivers(int $perPage = 15)
    {
        return Driver::where('status', 'available')->paginate($perPage);
    }

    public function getDriverHistory($driverId, int $perPage = 15)
    {
        // Asumsi DeliveryHistory memiliki relasi dengan Driver dan Order
        return DeliveryHistory::where('driver_id', $driverId)->with('order')->paginate($perPage);
    }
}
