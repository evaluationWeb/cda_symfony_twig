<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiCategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/api/categories', name: 'api_category_all')]
    public function getAllCategories(): Response
    {
        return $this->json(
            $this->categoryRepository->findAll(),
            200,
            [
                "Access-Control-Allow-Origin" => "*",
                "Content-Type" => "application/json"
            ],
            ['groups' => 'category:read']
        );
    }

    #[Route('/api/category', name:'app_category_add', methods: ['POST'])]
    public function addCategory(Request $request):Response {
        //Récupération le body de la requête
        $request = $request->getContent();
        //Convertir en objet Category
        $category = $this->serializer->deserialize($request, Category::class, 'json');
        //Tester si la catégorie n'existe pas
        if(!$this->categoryRepository->findOneBy(["name"=>$category->getName()])) {
            $this->em->persist($category);
            $this->em->flush();
            $code = 201;
        }
        //Sinon elle existe déjà
        else{
            $category = "Category existe déjà";
            $code = 400;
        }
        return $this->json($category, $code, [
            "Access-Control-Allow-Origin" => "*",
            "Content-Type" => "application/json"
        ], []);
    }
}
