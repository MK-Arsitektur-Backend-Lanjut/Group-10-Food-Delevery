<?php

namespace App\Http\Controllers;

use App\Repositories\DriverRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class DriverController extends Controller
{
    protected $driverRepository;

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
        $this->middleware('auth:api', ['except' => ['login', 'register']]); // JWT auth untuk semua kecuali login/register
    }

    // CRUD dasar
    public function index()
    {
        return Response::json($this->driverRepository->all());
    }

    public function show($id)
    {
        return Response::json($this->driverRepository->find($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle' => 'required|string|max:255',
            'status' => 'required|in:available,unavailable',
            'email' => 'required|email|unique:drivers',
            'password' => 'required|string|min:6',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']); // Hash password
        return Response::json($this->driverRepository->create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'vehicle' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:available,unavailable',
        ]);

        return Response::json($this->driverRepository->update($id, $request->all()));
    }

    public function destroy($id)
    {
        $this->driverRepository->delete($id);
        return Response::noContent();
    }

    // Fitur baru: Pencarian driver tersedia
    public function available()
    {
        return Response::json($this->driverRepository->getAvailableDrivers());
    }

    // Fitur baru: Riwayat pengantaran driver
    public function history($id)
    {
        // Driver hanya bisa melihat riwayat miliknya sendiri.
        if ((int) Auth::guard('api')->id() !== (int) $id) {
            return Response::json(['error' => 'Unauthorized'], 403);
        }

        return Response::json($this->driverRepository->getDriverHistory($id));
    }

    // Autentikasi: Login untuk Driver
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        return Response::json(['token' => $token]);
    }

    // Autentikasi: Register untuk Driver
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:drivers',
            'password' => 'required|string|min:6',
            'vehicle' => 'required|string|max:255',
        ]);

        // @dd($request->all());
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'available'; // Default status
        $driver = $this->driverRepository->create($data);
        $token = JWTAuth::fromUser($driver);
        return Response::json(['driver' => $driver, 'token' => $token], 201);
    }
}