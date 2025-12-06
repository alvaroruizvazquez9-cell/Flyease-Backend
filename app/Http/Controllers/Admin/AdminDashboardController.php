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

    public function stats(Request $request)
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

        // Chart Data
        $range = $request->input('range', '30d');
        $chartData = [];
        $now = \Carbon\Carbon::now();

        if ($range === '24h') {
            // Last 24 Hours - Hourly Data
            for ($i = 23; $i >= 0; $i--) {
                $date = $now->copy()->subHours($i);
                $dateStart = $date->copy()->startOfHour();
                $dateEnd = $date->copy()->endOfHour();
                $label = $date->format('H:00');

                $dayStats = Booking::whereBetween('created_at', [$dateStart, $dateEnd])
                    ->selectRaw('count(*) as count, sum(case when status = "confirmed" then total_price else 0 end) as revenue')
                    ->first();

                $chartData[] = [
                    'name' => $label,
                    'revenue' => (float) ($dayStats->revenue ?? 0),
                    'bookings' => (int) ($dayStats->count ?? 0)
                ];
            }
        } elseif ($range === 'year') {
            // Last 12 Months - Monthly Data
            for ($i = 11; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i);
                $monthStart = $date->copy()->startOfMonth();
                $monthEnd = $date->copy()->endOfMonth();
                $label = $date->format('M Y');

                $dayStats = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->selectRaw('count(*) as count, sum(case when status = "confirmed" then total_price else 0 end) as revenue')
                    ->first();

                $chartData[] = [
                    'name' => $label,
                    'revenue' => (float) ($dayStats->revenue ?? 0),
                    'bookings' => (int) ($dayStats->count ?? 0)
                ];
            }
        } else {
            // Default: Daily Data (7d or 30d)
            $days = ($range === '7d') ? 7 : 30;

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $dateString = $date->format('Y-m-d');
                $label = $date->format('d M');

                $dayStats = Booking::whereDate('created_at', $dateString)
                    ->selectRaw('count(*) as count, sum(case when status = "confirmed" then total_price else 0 end) as revenue')
                    ->first();

                $chartData[] = [
                    'name' => $label,
                    'revenue' => (float) ($dayStats->revenue ?? 0),
                    'bookings' => (int) ($dayStats->count ?? 0)
                ];
            }
        }

        $stats['chart_data'] = $chartData;

        return $this->success($stats);
    }
}
