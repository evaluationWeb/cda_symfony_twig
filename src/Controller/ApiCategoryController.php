<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiCategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}

    #[Route('/api/categories', name: 'api_category_all')]
    public function getAllCategories(): Response
    {
        return $this->json(
            $this->categoryRepository->findAll(),
            200,
            [],
            ['groups' => 'category:read']
        );
    }
}
