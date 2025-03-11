<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AccountRepository;
use App\Entity\Account;
use App\Form\AccountType;
use App\Service\AccountService;


final class UserController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly AccountService $accountService
    ) {}

    #[Route('/register', name: 'app_user_register')]
    public function register(): Response
    {
        return $this->render('user/register.html.twig');
    }

    #[Route('/login', name: 'app_user_login')]
    public function login(): Response
    {
        return $this->render('user/login.html.twig');
    }


    //Méthode qui va afficher tous les comptes
    #[Route('/accounts', name: 'app_user_accounts')]
    public function showAllAccounts(): Response
    {   
        try {
            $accounts = $this->accountService->getAll();
        } catch (\Exception $e) {
            $erreur = $e->getMessage();
        }
        
        return $this->render('user/accounts.html.twig', [
            "accounts" => $accounts??null,
            "erreur" => $erreur??null
        ]);
    }


    //Méthode qui ajoute un utilisateur en BDD
    #[Route('/account/add', name:'app_account_add')]
    public function addAccount(Request $request): Response
    {

        $user = new Account();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        $type = "";
        $msg = "";
        //test si le formulaire est submit
        if($form->isSubmitted() && $form->isValid()) {
            try {
                //Appel de la méthode save d'AccountService
                $this->accountService->save($user);
                $type = "success";
                $msg = "Le compte a ete ajoute en BDD";
            } 
            //Capturer les exceptions (erreurs)
            catch (\Exception $e) {
                $type = "danger";
                $msg = $e->getMessage();
            }
            $this->addFlash($type, $msg);
        }

        return $this->render('user/addaccount.html.twig',[
            'formulaire' =>$form
        ]);
    }
}
