<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class ApiAccountController extends AbstractController
{

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly DecoderInterface $decoder
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

    //Méthode pour ajouter un compte
    #[Route('/api/account', name: 'api_account_add', methods: ['POST'])]
    public function addAccount(Request $request): Response
    {
        $json = $request->getContent();
        $account = $this->serializer->deserialize($json, Account::class, 'json');
        //test si les champs sont remplis
        if ($account->getFirstname() && $account->getLastname() && $account->getEmail() && $account->getPassword() && $account->getRoles()) {
            //Test si le compte n'existe pas
            if (!$this->accountRepository->findOneBy(["email" => $account->getEmail()])) {
                $account->setPassword($this->hasher->hashPassword($account, $account->getPassword()));
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

    //Méthode pour mettre à jour un compte( nom et prénom par son Email)
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

    //Méthode pour supprimer un compte
    #[Route('/api/account/{id}', name: 'api_account_delete', methods: ['DELETE'])]
    public function deleteAccount(mixed $id): Response
    {
        //test si l'id est un nombre    
        if (!is_numeric($id)) {
            $code = 400;
            $account = "Id invalide";
        }
        //Sinon l'id est un nombre
        else {
            $account = $this->accountRepository->find($id);
            //Test si le compte existe
            if ($account) {
                //récupération d'un article du compte
                $article =  $this->articleRepository->findOneBy(["author" => $account]);
                //si le compte n'a pas d'articles
                if (!$article) {
                    $this->em->remove($account);
                    $this->em->flush();
                    $code = 200;
                    $account = "Compte supprime";
                }
                //sinon le compte a des articles
                else {
                    $account = "Compte a des articles";
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
            []
        );
    }


    //Méthode pour modifier son mot de passe
    #[Route('/api/account', name: 'api_account_password', methods: ['PATCH'])]
    public function updatePassword(Request $request) :Response {
        $json = $request->getContent();
        //Test si le json est vide
        if (empty($json)) {
            $account = "Json invalide";
            $code = 400;
        }
        else {
            //Récupér les valeurs dans je json -> tableau
            $account = $this->decoder->decode($json, 'json');
            //Récupérer le compte
            $recup = $this->accountRepository->findOneBy(["email" => $account["email"]]);
            //Test si le compte existe
            if($recup) {

                //Véfifier le mot de passe
                if($this->hasher->isPasswordValid($recup, $account["old_password"])) {
                    //Hasher le nouveau mot de passe
                    $recup->setPassword($this->hasher->hashPassword($recup, $account["new_password"]));
                    $this->em->persist($recup);
                    $this->em->flush();
                    $account = $recup;
                    $code = 200;
                }
                //Sinon le mot de passe est incorrect
                else {
                    $account = "Mot de passe incorrect";
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
            ['groups' => 'account:update']
        );
    }
}
