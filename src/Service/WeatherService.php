<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    public function __construct(
        private readonly string $key,
        private readonly HttpClientInterface $httpClient
    ) {}

    public function test(): string
    {
        return $this->key;
    }


    public function getWeather():array {
        //Récupérer la réponse
        $response = $this->httpClient->request(
            'GET',
            'https://api.openweathermap.org/data/2.5/weather?lon=1.44&lat=43.6&appid=' . $this->key
        );
        return $response->toArray();
    }
}
