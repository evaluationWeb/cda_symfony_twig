<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VariableController extends AbstractController
{
    #[Route('/variable/{message}/{name}', name: 'app_variable')]
    public function index($message, $name): Response
    {
        return $this->render('variable/index.html.twig', [
            'message' => $message,
            'content' => $name
        ]);
    }
}
