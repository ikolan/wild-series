<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        '/{program}/seasons/{season}',
        requirements: ['programId' => '\d+', 'id' => '\d+'],
        methods: ["GET"],
        name: 'season_show'
    )]
    #[Entity('program', options: ['id' => 'program'])]
    #[Entity('season', options: ['id' => 'season'])]
    public function seasonShow(Program $program, Season $season): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season
        ]);
    }

    #[Route(
        '/{program}/seasons/{season}/episode/{episode}',
        requirements: [
            'program' => '\d+',
            'season' => '\d+',
            'episode' => '\d+'
        ],
        methods: ["GET"],
        name: 'episode_show'
    )]
    #[Entity('program', options: ['id' => 'program'])]
    #[Entity('season', options: ['id' => 'season'])]
    #[Entity('episode', options: ['id' => 'episode'])]
    public function episodeShow(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }

    #[Route('/new', methods: ["GET", "POST"], name: "new")]
    public function new(Request $request, ProgramRepository $programRepository): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $programRepository->save($program, true);
            return $this->redirectToRoute('program_index');
        }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form
        ]);
    }
}
