<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MapServices
{
    public function getDistant($destination, $origin){

        $key = env('GOOGLE_API_KEY', '');

        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            "origin" => $origin,
            "destination" => $destination,
            "language" => "id",
            "key" => $key,
        ]);

        if ($response->successful()) {
            $result = $response->json();
            dd($result['routes'][0]['legs'][0]['distance']['value']);
        } else {
            $errors = json_decode($response->getBody()->getContents());
            dd($errors);
        }
    }
}