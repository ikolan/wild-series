<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            "categories" => $categories
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ["GET"], name: 'show')]
    public function show(Category $category): Response
    {
        if (is_null($category)) {
            throw $this->createNotFoundException();
        }

        $programs = $category->getPrograms();

        return $this->render('category/show.html.twig', [
            "category" => $category,
            "programs" => $programs
        ]);
    }
}
