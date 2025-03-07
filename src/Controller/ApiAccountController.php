<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class ApiAccountController extends AbstractController
{

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $em
    ) {}


    // This route returns all accounts in JSON format
    #[Route('/api/accounts', name: 'api_account_all')]
    public function getAllAccounts(): Response
    {
        return $this->json(
            $this->accountRepository->findAll(),
            200,
            [
                "Access-Control-Allow-Origin" => $this->getParameter('allowed_origin'),
            ],
            ['groups' => 'account:read']
        );
    }


    #[Route('/api/account', name: 'api_account_add', methods: ['POST'])]
    public function addAccount(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $json = $request->getContent();
        $account = $this->serializer->deserialize($json, Account::class, 'json');
        //test si les champs sont remplis
        if ($account->getFirstname() && $account->getLastname() && $account->getEmail() && $account->getPassword() && $account->getRoles()) {
            //Test si le compte n'existe pas
            if (!$this->accountRepository->findOneBy(["email" => $account->getEmail()])) {
                $account->setPassword($hasher->hashPassword($account, $account->getPassword()));
                $this->em->persist($account);
                $this->em->flush();
                $code = 201;
            }
            //Sinon il existe déja
            else {
                $account = "Account existe déja";
                $code = 400;
            }
        }
        //Sinon les champs ne spont pas remplis
        else {
            $account = "Veuillez remplir tous les champs";
            $code = 400;
        }
        //Retourner la réponse json
        return $this->json($account, $code, [
            "Access-Control-Allow-Origin" => "*",
        ], ["groups" => "account:create"]);
    }


    #[Route('/api/account', name: 'api_account_update', methods: ['PUT'])]
    public function updateAcount(Request $request): Response
    {
        $json = $request->getContent();
        //dd($json);
        //Test si le json est vide
        if (empty($json)) {
            $account = "Json invalide";
            $code = 400;
        }
        //Sinon le json est valide
        else {

            $account = $this->serializer->deserialize($json, Account::class, 'json');
            $recup = $this->accountRepository->findOneBy(["email" => $account->getEmail()]);

            //Test si le compte existe
            if ($recup) {
                //Test si les champs sont remplis
                if ($account->getFirstname() && $account->getLastname()) {
                    $recup
                        ->setFirstname($account->getFirstname())
                        ->setLastname($account->getLastname());
                    $this->em->persist($recup);
                    $this->em->flush();
                    $code = 200;
                    $account = $recup;
                }
                //Sinon les champs ne sont pas remplis
                else {
                    $account = "Veuillez remplir tous les champs";
                    $code = 400;
                }
            }
            //Sinon le compte n'existe pas
            else {
                $account = "Pas de compte trouve";
                $code = 404;
            }
        }

        return $this->json(
            $account,
            $code,
            [
                "Access-Control-Allow-Origin" => "*",
            ],
            ["groups" => "account:update"]
        );
    }
}
