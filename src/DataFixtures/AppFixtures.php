<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Account;
use App\Entity\Article;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Création d'une instance de Faker
        $faker = Faker\Factory::create('fr_FR');
        
        //tableau de comptes
        $accounts = [];
        
        //Création des 50 comptes
        for ($i=0; $i < 50 ; $i++) { 
            //Ajouter un compte
            $account = new Account();
            $account->setFirstname($faker->firstName())
                    ->setLastname($faker->lastName())
                    ->setEmail($faker->unique()->freeEmail())
                    ->setPassword($faker->password())
                    ->setRoles("ROLE_USER");
            //Ajout en cache
            $manager->persist($account);
            $accounts[] = $account;
        }
        
        for ($i=0; $i < 100; $i++) { 
           $article = new Article();
           $article->setTitle($faker->sentence(3))
                   ->setContent($faker->realText(400, 4))
                   ->setCreateAt(new \DateTimeImmutable($faker->date()))
                   ->setAuthor($accounts[$faker->numberBetween(0, 49)]);
            $manager->persist($article);
        }
        //Enregistrement en base de données     
        $manager->flush();
    }
}
