<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Events\TripEnded;
use App\Events\TripStarted;
use App\Events\TripAccepted;
use Illuminate\Http\Request;
use App\Events\TripLocationUpdated;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);

        return $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name',
        ]));
    }

    public function show(Request $request, Trip $trip)
    {
        // is the trip is associated with the authenticated user?
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }

        if ($trip->driver && $request->user()->driver) {
            if ($trip->driver->id === $request->driver()->id) {
                return $trip;
            }
        }
        return response()->json('message', ' Cannot find this trip.');
    }


    public function accept(Request $request, Trip $trip)
    {
        // a driver accepts a trip
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');

        TripAccepted::dispatch($trip, $request->user());

        return $trip;
    }

    public function start(Request $request, Trip $trip)
    {
        // a driver has started taking a passenger to their destination
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip;
    }
    public function end(Request $request, Trip $trip)
    {
        // a driver has ended a trip
        $trip->update([
            'is_completed' => true
        ]);

        $trip->load('driver.user');

        TripEnded::broadcast($trip, $request->user());

        return $trip;
    }
    public function location(Request $request, Trip $trip)
    {
        // update the driver's current location
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripLocationUpdated::broadcast($trip, $request->user());

        return $trip;
    }
}
