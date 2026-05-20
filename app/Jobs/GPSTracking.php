<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GPSTracking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $businessId;
    public $points;
    public $date;
    public $start;

    public function __construct($userId, $businessId, $points, $date, $start)
    {
        $this->userId = $userId;
        $this->businessId = $businessId;
        $this->points = $points;
        $this->date = $date;
        $this->start = $start;
    }

    public function handle(): void
    {
        $points = is_array($this->points)
            ? $this->points
            : json_decode($this->points, true);

        if (!is_array($points) || empty($points)) {
            throw new \Exception('Invalid GPS points payload.');
        }

        DB::connection('mysql')->transaction(function () use ($points) {
            $firstPoint = $points[0];
            $lastPoint = $points[count($points) - 1];

            $tripDate = date('Y-m-d', strtotime($firstPoint['date'] ?? $this->date));
            $clockInTime = $firstPoint['date'] ?? $this->date;
            $startLocation = $firstPoint['point'] ?? null;

            $tripId = null;

            if ((int) $this->start === 1) {
                $tripId = DB::connection('mysql')->table('gps_trips')->insertGetId([
                    'user_id'        => $this->userId,
                    'business_id'    => $this->businessId,
                    'trip_date'      => $tripDate,
                    'clock_in_time'  => $clockInTime,
                    'start_location' => $startLocation,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                $tripId = DB::connection('mysql')
                    ->table('gps_trips')
                    ->where('user_id', $this->userId)
                    ->whereDate('trip_date', $tripDate)
                    ->orderByDesc('id')
                    ->value('id');

                if (!$tripId) {
                    $tripId = DB::connection('mysql')->table('gps_trips')->insertGetId([
                        'user_id'        => $this->userId,
                        'business_id'    => $this->businessId,
                        'trip_date'      => $tripDate,
                        'clock_in_time'  => $clockInTime,
                        'start_location' => $startLocation,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }

            $rows = [];

            foreach ($points as $item) {
                $rows[] = [
                    'trip_id'    => $tripId,
                    'user_id'    => $this->userId,
                    'location'   => $item['point'] ?? null,
                    'gps_time'   => $item['date'] ?? $this->date,
                    'created_at' => now(),
                ];
            }

            DB::connection('mysql')->table('gps_points')->insert($rows);

            $endLocation = $lastPoint['point'] ?? null;
            $clockOutTime = $lastPoint['date'] ?? null;

            $lastStatus = (int) ($lastPoint['status'] ?? 0);

            if ($lastStatus === 2) {
                DB::connection('mysql')->table('gps_trips')
                    ->where('id', $tripId)
                    ->update([
                        'end_location'  => $endLocation,
                        'clock_out_time'=> $clockOutTime,
                        'updated_at'    => now(),
                    ]);
            }
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GPS Tracking Job Failed', [
            'message' => $exception->getMessage(),
        ]);
    }
}
