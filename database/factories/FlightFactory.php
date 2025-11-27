<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $airlines = ['Iberia', 'Ryanair', 'Air France', 'Lufthansa', 'British Airways', 'Vueling'];
        $cities = ['Madrid', 'Londres', 'París', 'Berlín', 'Roma', 'Nueva York', 'Tokio'];

        $origin = $this->faker->randomElement($cities);
        // Ensure destination is different from origin
        $destination = $this->faker->randomElement(array_diff($cities, [$origin]));

        $departure = $this->faker->dateTimeBetween('now', '+2 months');
        // Arrival is 1-12 hours after departure
        $arrival = (clone $departure)->modify('+' . rand(1, 12) . ' hours');

        return [
            'flight_number' => strtoupper($this->faker->bothify('??####')),
            'airline' => $this->faker->randomElement($airlines),
            'origin' => $origin,
            'destination' => $destination,
            'departure_time' => $departure,
            'arrival_time' => $arrival,
            'price' => $this->faker->numberBetween(50, 500),
            'available_seats' => $this->faker->numberBetween(0, 200),
            'status' => 'scheduled',
        ];
    }
}
