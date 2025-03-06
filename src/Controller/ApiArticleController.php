<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;


final class ApiArticleController extends AbstractController {

    public function __construct(
        private ArticleRepository $articleRepository
    ) {}
    

    #[Route('/api/articles', name: 'api_articles_all')]
    public function getAllAccounts(): Response
    {
        return $this->json(
            $this->articleRepository->findAll(),
            200,
            [
                "Access-Control-Allow-Origin" => "*",
                "Content-Type" => "application/json"
            ],
            ['groups' => 'articles:read']
        );
    }


    #[Route('/api/article/{id}', name: 'api_article_get')]
    public function getArticleById(int $id) :Response {
        //Récupération de l'article
        $article = $this->articleRepository->find($id);
        $code = 200;
        //test si l'article existe pas
        if(!$article) {
            $article = "Article n'existe pas";
            $code = 404;
        }
        //retourner la réponse json
        return $this->json(
            $article,
            $code,
            [
                "Access-Control-Allow-Origin" => "*",
                "Content-Type" => "application/json"
            ],
            ['groups' => 'articles:read']
        );
    }
}