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
        $airlines = [
            'Iberia',
            'Ryanair',
            'Air France',
            'Lufthansa',
            'British Airways',
            'Vueling',
            'Emirates',
            'Qatar Airways',
            'Delta',
            'American Airlines',
            'United Airlines',
            'KLM',
            'Turkish Airlines',
            'Singapore Airlines',
            'Cathay Pacific'
        ];

        $cities = [
            'Madrid',
            'Londres',
            'París',
            'Berlín',
            'Roma',
            'Nueva York',
            'Tokio',
            'Dubai',
            'Singapur',
            'Los Ángeles',
            'Sídney',
            'Barcelona',
            'Amsterdam',
            'Lisboa',
            'Buenos Aires',
            'Miami',
            'Toronto',
            'Hong Kong',
            'Bangkok'
        ];

        $origin = $this->faker->randomElement($cities);
        $destination = $this->faker->randomElement(array_diff($cities, [$origin]));

        $departure = $this->faker->dateTimeBetween('now', '+6 months');
        // Arrival is 2-14 hours after departure (slightly longer range for long haul)
        $arrival = (clone $departure)->modify('+' . rand(2, 14) . ' hours');

        return [
            'flight_number' => strtoupper($this->faker->bothify('??####')),
            'airline' => $this->faker->randomElement($airlines),
            'origin' => $origin,
            'destination' => $destination,
            'departure_time' => $departure,
            'arrival_time' => $arrival,
            'price' => $this->faker->numberBetween(50, 1200), // Adjusted price range
            'available_seats' => $this->faker->numberBetween(0, 300), // Adjusted seat range
            'status' => 'scheduled',
        ];
    }
}
