<?php

namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\WeatherType;



final class WeatherController extends AbstractController
{
    public function __construct(
        private readonly WeatherService $weather
    ) {}

    #[Route('/weather', name: 'app_weather')]
    public function index(): Response
    {

        return $this->render('weather/meteo.html.twig', [
            'meteo' => $this->weather->getWeather(),
        ]);
    }

    #[Route('/weather/city', name:'app_weather_city')]
    public function showWeatherByCity(Request $request) {
        
        $form = $this->createForm(WeatherType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
           $meteo = $this->weather->getWeatherByCity($request->request->all('weather')["city"]);
        }

        return $this->render('weather/meteo-city.html.twig',[
            "form" => $form,
            "meteo" => $meteo??null
        ]);
    }
}
