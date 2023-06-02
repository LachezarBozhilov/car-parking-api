<?php

namespace Database\Factories;

use App\Models\Parking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class ParkingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Parking::class;

    public function definition(): array
    {
        return [
            'vehicle_number' => fake()->word(),
            'vehicle_category' => fake()->randomElement(['A', 'B', 'C']),  
            'vehicle_card' => fake()->randomElement(['Silver', 'Gold', 'Platinum' ,""]),
            'entry_time' => Carbon::now(rand(1, 24)),
            // 'exit_time' =>
        ];
    }
}
