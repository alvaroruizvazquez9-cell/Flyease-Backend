<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index(Request $request)
    {
        $query = Flight::query()
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0);

        if ($request->origin) {
            $query->where('origin', $request->origin);
        }
        if ($request->destination) {
            $query->where('destination', $request->destination);
        }
        if ($request->date) {
            $query->whereDate('departure_time', $request->date);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        $flights = $query->paginate(10);

        return $this->success($flights);
    }

    public function show($id)
    {
        $flight = Flight::findOrFail($id);
        return $this->success($flight);
    }
}