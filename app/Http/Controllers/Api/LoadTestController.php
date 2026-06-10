<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use OpenApi\Attributes as OA;

class LoadTestController extends Controller
{
    #[OA\Get(
        path: '/api/v1/load-test/results',
        summary: 'Get the latest K6 Load Test Results',
        tags: ['Load Testing'],
        servers: [new OA\Server(url: '/')],
        responses: [
            new OA\Response(response: 200, description: 'Load Test Results Data'),
            new OA\Response(response: 404, description: 'No test results found')
        ]
    )]
    public function results(): JsonResponse
    {
        $path = storage_path('app/k6_result.json');

        if (!File::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada hasil load test. Silakan jalankan K6 dengan flag --summary-export terlebih dahulu.',
            ], 404);
        }

        $data = json_decode(File::get($path), true);

        return response()->json([
            'success' => true,
            'message' => 'Hasil Load Test K6',
            'data' => $data['metrics'] ?? $data
        ]);
    }
}
