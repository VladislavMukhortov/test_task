<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationsRequest;
use App\Jobs\GeocodeUserLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Queue;

class UserLocationController extends Controller
{
    /**
     * Save user location
     *
     * @param LocationsRequest $request
     * @return JsonResponse
     */
    public function saveLocation(LocationsRequest $request): JsonResponse
    {
        $userLocation = auth()->user()->userLocations()->create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $isQueued = Queue::push(new GeocodeUserLocation($userLocation));

        if ($isQueued) {
            return response()->json([
                'success' => 'true',
                'message' => 'Location saved successfully'
            ]);
        } else {
            return response()->json([
                'success' => 'false',
                'message' => 'Failed to add task to the queue'
            ], 500);
        }
    }
}
