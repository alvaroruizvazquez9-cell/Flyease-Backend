<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index()
    {
        $bookings = auth()->user()->bookings()->with(['flight', 'payment'])->latest()->paginate(10);
        return $this->success($bookings);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'flight_id' => 'required|exists:flights,id',
                'passengers' => 'required|integer|min:1|max:10',
            ]);

            $flight = Flight::findOrFail($request->flight_id);

            if ($flight->available_seats < $request->passengers) {
                return $this->error('No hay suficientes asientos disponibles', 400);
            }

            $total = $flight->price * $request->passengers;

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'flight_id' => $flight->id,
                'booking_reference' => 'BOOK-' . strtoupper(Str::random(8)),
                'passengers' => $request->passengers,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            return $this->success($booking, 'Reserva iniciada. Procede al pago.', 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating booking: ' . $e->getMessage());
            return $this->error('Error al crear la reserva: ' . $e->getMessage(), 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($booking->status !== 'pending') {
            return $this->error('Reserva ya procesada o cancelada');
        }

        if ($request->amount != $booking->total_price) {
            return $this->error('El monto del pago no coincide con el total de la reserva', 400);
        }

        // Simular pago exitoso
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'completed',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        ]);

        $flight = $booking->flight;
        $flight->decrement('available_seats', $booking->passengers);

        $booking->update([
            'status' => 'confirmed',
            'payment_intent_id' => $payment->transaction_id // Usamos esto como referencia cruzada si es necesario
        ]);

        return $this->success([
            'booking' => $booking,
            'payment' => $payment
        ], 'Pago confirmado. Reserva exitosa.');
    }

    public function cancel($id)
    {
        $booking = auth()->user()->bookings()->findOrFail($id);

        // Allow cancelling confirmed bookings (with seat restoration)
        if ($booking->status === 'confirmed') {
            // Restore seats to the flight
            $booking->flight->increment('available_seats', $booking->passengers);
        }

        $booking->update(['status' => 'cancelled']);
        return $this->success(null, 'Reserva cancelada');
    }

    public function destroy($id)
    {
        $booking = auth()->user()->bookings()->findOrFail($id);

        // If booking is confirmed, restore seats before deleting
        if ($booking->status === 'confirmed') {
            $booking->flight->increment('available_seats', $booking->passengers);
        }

        $booking->delete();
        return $this->success(null, 'Reserva eliminada correctamente');
    }
}