<?php

namespace App\Http\Controllers;

use App\Repositories\DriverRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use OpenApi\Attributes as OA;
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
    #[OA\Get(
        path: '/api/drivers',
        summary: 'Get list of drivers',
        tags: ['Drivers'],
        servers: [new OA\Server(url: '/')],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Driver'))),
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $drivers = $this->driverRepository->all($perPage);

        return Response::json([
            'success' => true,
            'message' => 'Drivers retrieved successfully',
            'data' => $drivers->items(),
            'meta' => [
                'current_page' => $drivers->currentPage(),
                'last_page' => $drivers->lastPage(),
                'per_page' => $drivers->perPage(),
                'total' => $drivers->total(),
            ],
        ]);
    }

    #[OA\Get(
        path: '/api/drivers/all',
        summary: 'Get all drivers with cursor pagination (for bulk fetch)',
        tags: ['Drivers'],
        servers: [new OA\Server(url: '/')],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 500)),
            new OA\Parameter(name: 'cursor', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Driver'))),
        ]
    )]
    public function all(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 500), 1000);
        $drivers = $this->driverRepository->cursorPaginate($perPage);

        return Response::json([
            'success' => true,
            'message' => 'Drivers bulk retrieved successfully',
            'data' => $drivers->items(),
            'meta' => [
                'next_cursor' => $drivers->nextCursor()?->encode(),
                'prev_cursor' => $drivers->previousCursor()?->encode(),
                'per_page' => $drivers->perPage(),
            ],
        ]);
    }

    #[OA\Get(
        path: '/api/drivers/{id}',
        summary: 'Get driver details',
        tags: ['Drivers'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Driver')),
        ]
    )]
    public function show($id)
    {
        return Response::json($this->driverRepository->find($id));
    }

    #[OA\Post(
        path: '/api/drivers',
        summary: 'Create a new driver',
        tags: ['Drivers'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreDriverRequest')
        ),
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/Driver')),
        ]
    )]
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

    #[OA\Put(
        path: '/api/drivers/{id}',
        summary: 'Update an existing driver',
        tags: ['Drivers'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateDriverRequest')
        ),
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(ref: '#/components/schemas/Driver')),
        ]
    )]
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'vehicle' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:available,unavailable',
        ]);

        return Response::json($this->driverRepository->update($id, $request->all()));
    }

    #[OA\Delete(
        path: '/api/drivers/{id}',
        summary: 'Delete a driver',
        tags: ['Drivers'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 204, description: 'No content'),
        ]
    )]
    public function destroy($id)
    {
        $this->driverRepository->delete($id);

        return Response::noContent();
    }

    // Fitur baru: Pencarian driver tersedia
    #[OA\Get(
        path: '/api/drivers/available',
        summary: 'Get available drivers',
        tags: ['Drivers'],
        servers: [new OA\Server(url: '/')],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Driver'))),
        ]
    )]
    public function available(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $drivers = $this->driverRepository->getAvailableDrivers($perPage);

        return Response::json([
            'success' => true,
            'message' => 'Available drivers retrieved successfully',
            'data' => $drivers->items(),
            'meta' => [
                'current_page' => $drivers->currentPage(),
                'last_page' => $drivers->lastPage(),
                'per_page' => $drivers->perPage(),
                'total' => $drivers->total(),
            ],
        ]);
    }

    // Fitur baru: Riwayat pengantaran driver
    #[OA\Get(
        path: '/api/drivers/{id}/history',
        summary: 'Get driver delivery history',
        tags: ['Drivers'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/DeliveryHistory'))),
            new OA\Response(response: 403, description: 'Unauthorized'),
        ]
    )]
    public function history(Request $request, $id)
    {
        // Driver hanya bisa melihat riwayat miliknya sendiri.
        if ((int) Auth::guard('api')->id() !== (int) $id) {
            return Response::json(['error' => 'Unauthorized'], 403);
        }

        $perPage = $request->input('per_page', 15);
        $history = $this->driverRepository->getDriverHistory($id, $perPage);

        return Response::json([
            'success' => true,
            'message' => 'Driver history retrieved successfully',
            'data' => $history->items(),
            'meta' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
            ],
        ]);
    }

    // Autentikasi: Login untuk Driver
    #[OA\Post(
        path: '/api/drivers/login',
        summary: 'Driver Login',
        tags: ['Drivers'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DriverLoginRequest')
        ),
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'token', type: 'string')])
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
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
    #[OA\Post(
        path: '/api/drivers/register',
        summary: 'Driver Registration',
        tags: ['Drivers'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DriverRegisterRequest')
        ),
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Success',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'driver', ref: '#/components/schemas/Driver'),
                    new OA\Property(property: 'token', type: 'string'),
                ])
            ),
        ]
    )]
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
