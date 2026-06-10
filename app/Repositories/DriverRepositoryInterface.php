<?php

namespace App\Repositories;

interface DriverRepositoryInterface
{
    public function all(int $perPage = 15);

    public function cursorPaginate(int $perPage = 500);

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getAvailableDrivers(int $perPage = 15);

    public function getDriverHistory($driverId, int $perPage = 15);
}
