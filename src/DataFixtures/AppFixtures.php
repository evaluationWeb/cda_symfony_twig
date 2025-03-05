<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Account;
use App\Entity\Article;
use App\Entity\Category;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Création d'une instance de Faker
        $faker = Faker\Factory::create('fr_FR');
        
        //tableau de comptes
        $accounts = [];
        $categories = [];
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
        
        //Création des 30 Category
        for ($i=0; $i < 30 ; $i++) { 
            $category = new Category();
            $category->setName($faker->unique()->jobTitle());
            $manager->persist($category);
            $categories[] = $category;
        }

        //Création des 100 articles
        for ($i=0; $i < 100; $i++) { 
           $article = new Article();
           $article->setTitle($faker->sentence(3))
                   ->setContent($faker->realText(400, 4))
                   ->setCreateAt(new \DateTimeImmutable($faker->date()))
                   ->setAuthor($accounts[$faker->numberBetween(0, 49)]);
            //Ajout des catégories
            $article->addCategory($categories[$faker->numberBetween(0, 9)]);
            $article->addCategory($categories[$faker->numberBetween(10, 19)]);
            $article->addCategory($categories[$faker->numberBetween(20, 29)]);
            $manager->persist($article);
        }
        //Enregistrement en base de données     
        $manager->flush();
    }
}
