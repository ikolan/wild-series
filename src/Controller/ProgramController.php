<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', methods: ["GET"], name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ["GET"], name: 'show')]
    public function show(Program $program): Response
    {
        if (is_null($program)) {
            throw $this->createNotFoundException();
        }

        return $this->render('program/show.html.twig', [
            'program' => $program
        ]);
    }

    #[Route(
        '/{programId}/seasons/{id}',
        requirements: ['programId' => '\d+', 'id' => '\d+'],
        methods: ["GET"],
        name: 'season_show'
    )]
    public function seasonShow(Season $season): Response
    {
        return $this->render('program/season_show.html.twig', [
            'season' => $season
        ]);
    }
}
