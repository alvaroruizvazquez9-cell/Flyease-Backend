<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index()
    {
        $bookings = auth()->user()->bookings()->with('flight')->latest()->paginate(10);
        return $this->success($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'passengers' => 'required|integer|min:1|max:10',
        ]);

        $flight = Flight::findOrFail($request->flight_id);

        if ($flight->available_seats < $request->passengers) {
            return $this->error('No hay suficientes asientos disponibles', 400);
        }

        $total = $flight->price * $request->passengers;

        Stripe::setApiKey(config('stripe.secret'));
        $paymentIntent = PaymentIntent::create([
            'amount' => $total * 100,
            'currency' => 'usd',
            'metadata' => [
                'flight_id' => $flight->id,
                'user_id' => auth()->id(),
            ],
        ]);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'flight_id' => $flight->id,
            'booking_reference' => 'BOOK-' . strtoupper(\Str::random(8)),
            'passengers' => $request->passengers,
            'total_price' => $total,
            'payment_intent_id' => $paymentIntent->id,
            'status' => 'pending',
        ]);

        return $this->success([
            'booking' => $booking,
            'client_secret' => $paymentIntent->client_secret,
        ], 'Reserva creada. Completa el pago en el frontend.', 201);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required',
        ]);

        $booking = Booking::where('payment_intent_id', $request->payment_intent_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($booking->status !== 'pending') {
            return $this->error('Reserva ya procesada');
        }

        Stripe::setApiKey(config('stripe.secret'));
        $pi = PaymentIntent::retrieve($request->payment_intent_id);

        if ($pi->status === 'succeeded') {
            $flight = $booking->flight;
            $flight->decrement('available_seats', $booking->passengers);
            $booking->update(['status' => 'confirmed']);
            return $this->success($booking, 'Pago confirmado. Reserva exitosa.');
        }

        return $this->error('Pago no completado', 400);
    }

    public function cancel($id)
    {
        $booking = auth()->user()->bookings()->findOrFail($id);

        if ($booking->status !== 'pending') {
            return $this->error('Solo se pueden cancelar reservas pendientes');
        }

        $booking->update(['status' => 'cancelled']);
        return $this->success(null, 'Reserva cancelada');
    }
}