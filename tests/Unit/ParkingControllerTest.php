<?php

namespace Tests\Unit;

use App\Models\Parking;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;

class ParkingControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test the checkAvailableSpots endpoint.
     *
     * @return void
     */
    public function testCheckAvailableSpots()
    {
        $vehicles = Parking::factory()->count(3)->create();

        $response = $this->json('get', '/api/parking/available-spots');

        $response->assertStatus(200);
        $response->assertJson(['free_spots' => 197]);
    }

    /**
     * Test the checkAmountDue endpoint for an existing vehicle.
     *
     * @return void
     */
    public function testCheckAmountDueForExistingVehicle()
    {
        $vehicle = Parking::factory()->create();

        $response = $this->get('/api/parking/amount-due/' . $vehicle->vehicle_number);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'payment_amount',
                'hours',
            ]);
    }

    /**
     * Test the checkAmountDue endpoint for a non-existing vehicle.
     *
     * @return void
     */
    public function testCheckAmountDueForNonExistingVehicle()
    {
        $response = $this->get('/api/parking/amount-due/123456');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Parked vehicle not found',
            ]);
    }

    /**
     * Test the registerVehicle endpoint with valid data.
     *
     * @return void
     */
    public function testRegisterVehicleWithValidData()
    {
        $vehicleData = [
            'vehicle_number' => 'ABC123',
            'vehicle_category' => 'A',

        ];

        $response = $this->post('/api/parking/register', $vehicleData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'vehicle',
            ]);
    }

    /**
     * Test the registerVehicle endpoint with missing data.
     *
     * @return void
     */
    public function testRegisterVehicleWithMissingData()
    {
        $response = $this->post('/api/parking/register', []);
        // dd($response);

        $response->assertStatus(422)
            ->assertJson([
                "message" => "Validation failed",
                "errors" => [
                    "vehicle_number" => [
                        "The vehicle number field is required."
                    ],
                    "vehicle_category" => [
                        "The vehicle category field is required."
                    ]
                ]
            ]);
    }

    /**
     * Test the deregisterVehicle endpoint for an existing vehicle.
     *
     * @return void
     */
    public function testDeregisterExistingVehicle()
    {
        $vehicle = Parking::factory()->create();
        // echo $vehicle;
        $response = $this->post('/api/parking/deregister/', [
            "vehicle_number" =>  $vehicle->vehicle_number
        ]);
        // dd($response->json());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'payment',
            ]);
    }

    /**
     * Test the deregisterVehicle endpoint for a non-existing vehicle.
     *
     * @return void
     */
    public function testDeregisterNonExistingVehicle()
    {
        $response = $this->post('/api/parking/deregister',["vehicle_number" => "1234"]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Vehicle not found',
            ]);
    }
}
