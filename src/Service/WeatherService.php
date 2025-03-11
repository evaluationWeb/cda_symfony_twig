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


    public function getWeather(): array
    {
        try {
            //Récupérer la réponse
            $response = $this->httpClient->request(
                'GET',
                'https://api.openweathermap.org/data/2.5/weather?lon=1.44&lat=43.6&appid=' . $this->key . '&units=metric'
            );
            $response = $response->toArray();
        } catch (\Exception $e) {
            $response = [
                "erreur" => "La ville n'existe pas",
                "cod" => 400
            ];
        }
        return $response;
    }


    public function getWeatherByCity(string $city)
    {
        try {
            //Récupérer la réponse
            $response = $this->httpClient->request(
                'GET',
                'https://api.openweathermap.org/data/2.5/weather?q=' . $city .
                    '&appid=' . $this->key . '&units=metric'
            );
            $response = $response->toArray();
        } catch (\Exception $e) {
            $response = [
                "erreur" => "La ville : " . $city. " n'existe pas",
                "cod" => 400
            ];
        }
        return $response;
    }
}
