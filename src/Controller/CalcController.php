<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalcController extends AbstractController
{
    #[Route('/calc/{nbr1}/{nbr2}/{operateur}', name: 'app_calc_calculatrice')]
    public function calculatice($nbr1, $nbr2, $operateur): Response
    {
      
        //Test si nbr1 ou nbr2 sont des nombres
        if(!is_numeric($nbr1) || !is_numeric($nbr2)) {
            $response = "Les deux nombres ne sont pas numériques";
        }
        //Sinon 
        else {
            switch ($operateur) {
                case 'add':
                    $response = "L'addition de $nbr1 et $nbr2 est égale au résultat : " . ($nbr1 + $nbr2) . '';
                    break;
                case 'sous':
                    $response = "La soustraction de $nbr1 et $nbr2 est égale au résultat : " . ($nbr1 - $nbr2) . '';
                    break;
                case 'multi':
                $response = "La multiplication de $nbr1 et $nbr2 est égale au résultat : " . ($nbr1 * $nbr2) . '';
                    break;
                case 'div':
                    if($nbr2 === 0) {
                        $response = "Division par zéro impossible";
                    }else{
                        $response = "La division de $nbr1 et $nbr2 est égale au résultat : " . ($nbr1 / $nbr2) . '';
                    }
                    break;
                default:
                    $response = "Opérateur inconnu";
                    break;
            }
        }
        return $this->render('calc/calculatrice.html.twig', [
            'nbr1' => $nbr1,
            'nbr2' => $nbr2,
            'operateur' => $operateur,
            'resultat' => $response,
        ]);
    }


    #[Route('/calcv2/{nbr1}/{nbr2}/{operateur}', name: 'app_calc_calculatricev2')]
    public function calculaticev2($nbr1,$nbr2, $operateur) {
        
        return $this->render('calc/calculatricev2.html.twig', [
            'nbr1' => $nbr1,
            'nbr2' => $nbr2,
            'operateur' => $operateur,
        ]);
    }
}
