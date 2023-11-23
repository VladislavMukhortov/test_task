<?php

namespace App\Jobs;

use App\UserLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodeUserLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public UserLocation $userLocation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserLocation $userLocation)
    {
        $this->userLocation = $userLocation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $latitude = $this->userLocation->latitude;
        $longitude = $this->userLocation->longitude;
        $address = "Адрес";
//        тут нужен ключ, который надо взять зарегестрировавшись в яндекс
//        $response = Http::get("https://geocode-maps.yandex.ru/1.x/?format=json&geocode={$longitude},{$latitude}");
//        $address = $response->json('response.GeoObjectCollection.featureMember.0.GeoObject.description');

        $this->userLocation->update(['address' => $address]);
    }
}
