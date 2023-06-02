<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParkingDeregisterRequest;
use App\Http\Requests\ParkingRegisterRequest;
use Illuminate\Http\Request;
use App\Models\Parking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class ParkingController extends Controller
{
    public function checkAvailableSpots()
    {
        $availableSpots = 200;
        $occupiedSpots = Parking::whereNull("exit_time")->count();

        $freeSpots = $availableSpots - $occupiedSpots;

        return response()->json(['free_spots' => $freeSpots]);
    }

    public function checkAmountDue($vehicleNumber)
    {
        $vehicle = Parking::where('vehicle_number', $vehicleNumber)->whereNull("exit_time")->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Parked vehicle not found'], 404);
        }

        $amountDue = $this->calculateAmountDue($vehicle);
        $entryTime =  Carbon::parse($vehicle->entry_time);

        $duration = $entryTime->diffinHours(Carbon::now());

        return response()->json(['payment_amount' => $amountDue, "hours" => $duration]);
    }


    public function registerVehicle(ParkingRegisterRequest $request)
    {
        // can replace Request with ParkingDeregister at requests, and remove the requests validation 
        // $validator = Validator::make($request->all(), [
        //     'vehicle_number' => 'required|string',
        //     'vehicle_category' => 'required|in:A,B,C',
        //     'vehicle_card' => 'in:Silver,Gold,Platinum',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'message' => 'Validation failed',
        //         'errors' => $validator->errors(),
        //     ], 422);
        // }

        $vehicleNumber = $request->vehicle_number;
        $vehicleCategory = $request->vehicle_category;
        $vehicleCard = $request->vehicle_card;

        $vehicle = Parking::create([
            'vehicle_number' => $vehicleNumber,
            'vehicle_category' => $vehicleCategory,
            'entry_time' => Carbon::now(),
            "vehicle_card" => $vehicleCard,
        ]);

        return response()->json(['message' => 'Vehicle registered successfully', 'vehicle' => $vehicle]);
    }


    public function deregisterVehicle(ParkingDeregisterRequest $request)
    {
        // can replace Request with ParkingDeregisterRequest at requests, and remove the requests validation         
        // $validator = Validator::make($request->all(), [
        //     'vehicle_number' => 'required|string',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'message' => 'Validation failed',
        //         'errors' => $validator->errors(),
        //     ], 422);
        // }

        $vehicleNumber = $request->vehicle_number;

        $parkedVehicle = Parking::where('vehicle_number', $vehicleNumber)->whereNull("exit_time")->first();

        if (!$parkedVehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        $amountDue = $this->calculateAmountDue($parkedVehicle);

        $parkedVehicle->exit_time = Carbon::now();
        $parkedVehicle->save();

        return response()->json(['message' => 'Vehicle deregistered successfully', 'payment' => $amountDue]);
    }
    // could go in ParkingService
    private function calculateAmountDue($parking)
    {
        $entryTime = $parking->entry_time;
        $exitTime = Carbon::now();

        $duration = $exitTime->diffInHours($entryTime);


        [$dayRate, $nightRate] = self::getRateByCategory($parking->vehicle_category);

        $paymentAmount = ($exitTime->hour >= 8 && $exitTime->hour < 18) ? ($dayRate * $duration) : ($nightRate * $duration);

        if ($parking->vehicle_card) {
            $totalAmount = self::applyDiscount($parking->vehicle_card, $paymentAmount);
        } else {
            $totalAmount = $paymentAmount;
        }

        return $totalAmount;
    }

    public function getRateByCategory($category)
    {
        switch ($category) {
            case 'A':
                $dayRate = 3;
                $nightRate = 2;
                break;
            case 'B':
                $dayRate = 6;
                $nightRate = 4;
                break;
            case 'C':
                $dayRate = 12;
                $nightRate = 8;
                break;
            default:
                throw new Exception("Invalid Vehicle category");
                return null;
        }
        return [$dayRate, $nightRate];
    }

    public function applyDiscount($getCardDiscountAmount, $amount)
    {
        switch ($getCardDiscountAmount) {
            case 'Silver':
                $discount = 0.10;
                break;
            case 'Gold':
                $discount = 0.15;
                break;
            case 'Platinum':
                $discount = 0.20;
                break;
            default:
                $discount = 0;
                break;
        }
        $discountedPrice = $amount - ($amount * $discount);
        return $discountedPrice;
    }
}
