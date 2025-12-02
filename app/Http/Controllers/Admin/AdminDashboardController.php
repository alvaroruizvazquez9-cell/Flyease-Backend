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

        return $this->success($stats);
    }
}
