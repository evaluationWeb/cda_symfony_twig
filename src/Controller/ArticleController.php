<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticleRepository;

final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository
    )
    {
    }


    #[Route('/articles', name: 'app_article_all')]
    public function showAllArticles(): Response
    {
        return $this->render('article/show_all_articles.html.twig', [
            "articles" => $this->articleRepository->findAll(),
        ]);
    }
}
