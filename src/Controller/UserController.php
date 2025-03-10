<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AccountRepository;
use App\Entity\Account;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;


final class UserController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em
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

    #[Route('/accounts', name: 'app_user_accounts')]
    public function showAllAccounts(): Response
    {
        return $this->render('user/accounts.html.twig', [
            "accounts" => $this->accountRepository->findAll()
        ]);
    }

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
                //Test si le compte n'existe pas
                if(!$this->accountRepository->findOneBy(['email' => $user->getEmail()])) {
                    $user->setRoles('ROLE_USER');
                    $this->em->persist($user);
                    $this->em->flush();
                    $type = "success";
                    $msg = "Compte ajouté avec succès";
                }
                //Sinon si le compte
                else {
                    $type = "danger";
                    $msg = "Email déjà utilisé";
                }
            }catch(\Exception $e) {
                $type = "danger";
                $msg="Enregistrement échoué";
            }
        $this->addFlash($type, $msg);
        }
        return $this->render('user/addaccount.html.twig',[
            'formulaire' =>$form
        ]);
    }
}
