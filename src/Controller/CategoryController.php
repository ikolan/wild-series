<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/{slug}', requirements: ['id' => '\d+'], methods: ["GET"], name: 'show')]
    public function show(Category $category, ProgramRepository $programRepository): Response
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

    #[Route('/new', methods: ["GET", "POST"], name: 'new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $categoryRepository->save($category, true);
            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }
}
