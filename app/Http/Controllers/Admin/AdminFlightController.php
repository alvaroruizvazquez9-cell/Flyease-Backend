<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;

class AdminFlightController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index()
    {
        $flights = Flight::latest()->paginate(20);
        return $this->success($flights);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'flight_number' => 'required|unique:flights',
            'airline' => 'required|string',
            'origin' => 'required|string|size:3',
            'destination' => 'required|string|size:3|different:origin',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:1',
        ]);

        $flight = Flight::create($data);
        return $this->success($flight, 'Vuelo creado', 201);
    }

    public function show($id)
    {
        $flight = Flight::findOrFail($id);
        return $this->success($flight);
    }

    public function update(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);

        $data = $request->validate([
            'flight_number' => 'sometimes|unique:flights,flight_number,' . $id,
            'airline' => 'sometimes|string',
            'origin' => 'sometimes|string|size:3',
            'destination' => 'sometimes|string|size:3|different:origin',
            'departure_time' => 'sometimes|date|after:now',
            'arrival_time' => 'sometimes|date|after:departure_time',
            'price' => 'sometimes|numeric|min:0',
            'available_seats' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:scheduled,delayed,cancelled,completed',
        ]);

        $flight->update($data);
        return $this->success($flight, 'Vuelo actualizado');
    }

    public function destroy($id)
    {
        $flight = Flight::findOrFail($id);
        $flight->delete();
        return $this->success(null, 'Vuelo eliminado');
    }
}