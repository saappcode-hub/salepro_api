<?php

namespace App\Http\Controllers\V1\GPSTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\GPSTracking;

class GPSTrackingController extends Controller
{
    public function tracking(Request $request)
    {
        $request->validate([
            'start' => 'nullable|integer',
            'date' => 'nullable|date',
            'points' => 'required|array|min:1',
            'points.*.point' => 'required|string',
            'points.*.date' => 'required|date',
            'points.*.status' => 'required|integer',
        ]);

        try {
            $userId = $request->user()->id;
            $businessId = $request->user()->business_id;
            $start = $request->start ?? 1;
            $date = $request->date
                ? jDateTimeFormat($request->date, 'Y-m-d H:i:s')
                : now()->format('Y-m-d H:i:s');

            $points = collect($request->points)->map(function ($item) {
                return [
                    'point'  => jPoints($item['point']),
                    'date'   => jDateTimeFormat($item['date'], 'Y-m-d H:i:s'),
                    'status' => (int) $item['status'],
                ];
            })->values()->all();

            $points = json_encode($points, JSON_UNESCAPED_UNICODE);

            GPSTracking::dispatch($userId, $businessId, $points, $date, $start);

            return response()->json([
                'message' => 'GPS tracked successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('GPS tracking failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'message' => 'GPS tracking failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
