<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index()
    {
        $bookings = Booking::with(['user', 'flight', 'payment'])->latest()->paginate(20);
        return $this->success($bookings);
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'flight', 'payment'])->findOrFail($id);
        return $this->success($booking);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Opcional: Reembolsar o lógica adicional
        $booking->update(['status' => 'cancelled']);

        // Liberar asientos si estaba confirmada
        if ($booking->status === 'confirmed') {
            $booking->flight->increment('available_seats', $booking->passengers);
        }

        return $this->success(null, 'Reserva cancelada correctamente');
    }
}
