<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Account;
use App\Repository\AccountRepository;

final class AccountService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AccountRepository $accountRepository
    ) {}

    //Ajouter un compte en BDD
    public function save(Account $account)
    {
        //Tester si les champs sont tous remplis
        if (
            $account->getFirstname() != "" && $account->getLastname() != "" && $account->getEmail() != "" &&
            $account->getPassword() != ""
        ) {
            //Test si le compte n'existe pas
            if(!$this->accountRepository->findOneBy(["email"=>$account->getEmail()])) {
                //Setter les paramètres 
                $account->setRoles("ROLE_USER");
                $this->em->persist($account);
                $this->em->flush();
            }
            else {
                throw new \Exception("Le compte existe déja");
            }
        }
        //Sinon les champs ne sont pas remplis
        else {
            throw new \Exception("Les champs ne sont pas tous remplis");
        }
    }

    //Récupérer la liste des comptes
    public function getAll() {
        $accounts = $this->accountRepository->findAll();
        //Test si on posséde des comptes en BDD
        if(!$accounts) {
            throw new \Exception("La liste est vide", 404);
        }
        return $accounts;
    }
}
