<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRouteRequest;
use App\UserLocation;
use Illuminate\Http\JsonResponse;

class UserRouteController extends Controller
{
    public function getUserRoute(UserRouteRequest $request): JsonResponse
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $userRoute = UserLocation::query()
            ->where('user_id', auth()->id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        foreach ($userRoute as &$location) {
            $latitude = $location->latitude;
            $longitude = $location->longitude;
            $address = "Адрес";

            // Запрос к Yandex Geocoding API для получения адреса
//            $response = Http::get("https://geocode-maps.yandex.ru/1.x/?format=json&geocode={$longitude},{$latitude}");
//            $address = $response->json('response.GeoObjectCollection.featureMember.0.GeoObject.description');
            // Добавляем адрес к текущей точке
            $location->address = $address;
        }

        return response()->json([
            'success' => true,
            'data' => $userRoute
        ]);
    }
}
