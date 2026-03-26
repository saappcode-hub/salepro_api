<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GPSTracking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $businessId;
    public $points;
    public $date;
    public $start;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $businessId, $points, $date, $start)
    {
        $this->userId = $userId;
        $this->businessId = $businessId;
        $this->points = $points;
        $this->date = $date;
        $this->start = $start;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Call stored procedure
        DB::connection('mysql')->statement('CALL gps_tracking(?, ?, ?, ?, ?, @trip_id)', [
            $this->userId,
            $this->businessId,
            $this->points,
            $this->date,
            $this->start
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure or implement retry logic
        \Log::error('GPS Tracking Job Failed: ' . $exception->getMessage());
    }
}