<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\AccountRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


final class ApiArticleController extends AbstractController
{

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly AccountRepository $accountRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer,
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


    #[Route('/api/article/{id}', name: 'api_article_get', methods:['GET'])]
    public function getArticleById(int $id): Response
    {
        //Récupération de l'article
        $article = $this->articleRepository->find($id);
        $code = 200;
        //test si l'article existe pas
        if (!$article) {
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


    #[Route('/api/article', name: 'api_article_add', methods: ['POST'])]
    public function addArticle(Request $request): Response
    {
        $json = $request->getContent();
        $article = $this->serializer->deserialize($json, Article::class, 'json');
        //test si les champs sont remplis
        if ($article->getTitle() && $article->getContent() && $article->getAuthor()) {
            //récupération du compte
            $article->setAuthor($this->accountRepository->findOneBy(["email" => $article->getAuthor()->getEmail()]));
            //Récupération des catégories
            foreach ($article->getCategories() as $key => $value) {
                $cat = $this->categoryRepository->findOneBy(["name" => $value->getName()]);
                $article->removeCategory($value);
                $article->addCategory($cat);
            }
            //Test l'article n'existe pas
            if (!$this->articleRepository->findOneBy([
                "title" => $article->getTitle(),
                "content" => $article->getContent()
            ])) {
                $this->entityManager->persist($article);
                $this->entityManager->flush();
                $code = 201;
            } else {
                $code = 400;
                $article = "Article existe deja";
            }
        } else {
            $code = 400;
            $article = "Champs non remplis";
        }
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
