<?php

namespace App\Http\Controllers\V1\GPSTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\GPSTracking;

class GPSTrackingController extends Controller
{
    public function tracking(Request $request)
    {    
        $userId = $request->user()->id;
        $businessId = $request->user()->business_id;
        $points = jPoints($request->points);
        $start = $request->start ?? 1;
        $date = $request->date 
            ? jDateTimeFormat($request->date, 'Y-m-d H:i:s') 
            : now()->format('Y-m-d H:i:s');

        // Dispatch the job to the queue
        GPSTracking::dispatch($userId, $businessId, $points, $date, $start);

        return response()->json([
            'message' => 'GPS tracked successfully'
        ]);
    }
}