<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    use \App\Traits\ApiResponse;

    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_flights' => Flight::count(),
            'total_bookings' => Booking::count(),
            'revenue' => Booking::where('status', 'confirmed')->sum('total_price'),
            'recent_bookings' => Booking::with(['user', 'flight'])
                ->latest()
                ->take(5)
                ->get()
        ];

        // Chart Data (Last 30 Days)
        $chartData = [];
        $today = \Carbon\Carbon::now();

        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $label = $date->format('d M');

            // Aggregate data for this day
            $dayStats = Booking::whereDate('created_at', $dateString)
                ->selectRaw('count(*) as count, sum(case when status = "confirmed" then total_price else 0 end) as revenue')
                ->first();

            $chartData[] = [
                'name' => $label,
                'revenue' => (float) ($dayStats->revenue ?? 0),
                'bookings' => (int) ($dayStats->count ?? 0)
            ];
        }

        $stats['chart_data'] = $chartData;

        return $this->success($stats);
    }
}
