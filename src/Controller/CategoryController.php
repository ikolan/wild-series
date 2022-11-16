<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
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
    public function show(
        int $id,
        CategoryRepository $categoryRepository,
        ProgramRepository $programRepository
    ): Response {
        $category = $categoryRepository->findOneBy(["id" => $id]);

        if (is_null($category)) {
            throw $this->createNotFoundException();
        }

        $programs = $programRepository->findBy(["category" => $category]);

        return $this->render('category/show.html.twig', [
            "category" => $category,
            "programs" => $programs
        ]);
    }
}
