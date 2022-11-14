<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_program_')]
class ProgramController extends AbstractController
{
    #[Route('/program/{id}', requirements: ['id' => '\d+'], methods: ["GET"], name: 'show')]
    public function show(int $id): Response
    {
        return $this->render('program/show.html.twig', [
            'id' => $id
        ]);
    }
}
