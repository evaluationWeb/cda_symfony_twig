<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Account;
use App\Repository\AccountRepository;


final class ApiAccountController extends AbstractController {

    public function __construct(
        private AccountRepository $accountRepository
    ) {}
    

    #[Route('/api/accounts', name: 'api_account_all')]
    public function getAllAccounts(): Response
    {
        return $this->json(
            $this->accountRepository->findAll(),
            200,
            [
                "Access-Control-Allow-Origin" => "*",
                "Content-Type" => "application/json"
            ],
            ['groups' => 'account:read']
        );
    }
}