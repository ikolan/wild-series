<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Program;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/show/{slug}', methods: ["GET"], name: 'show')]
    public function show(?Program $program): Response
    {
        if (is_null($program)) {
            throw $this->createNotFoundException();
        }

        return $this->render('program/show.html.twig', [
            'program' => $program
        ]);
    }

    #[Route(
        '/show/{slug}/s{season}',
        requirements: ["season" => "\d+"],
        methods: ["GET"],
        name: 'season_show'
    )]
    public function seasonShow(Program $program, int $season, SeasonRepository $seasonRepository): Response
    {
        $season = $seasonRepository->findOneBy([
            "program" => $program,
            "number" => $season
        ]);

        if (is_null($season)) {
            throw new NotFoundHttpException();
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season
        ]);
    }

    #[Route(
        '/show/{slug}/s{season}/e{episode}-{episodeSlug}',
        requirements: [
            'season' => '\d+',
            'episode' => '\d+'
        ],
        methods: ["GET", "POST"],
        name: 'episode_show'
    )]
    public function episodeShow(
        Request $request,
        Program $program,
        int $season,
        int $episode,
        string $episodeSlug,
        SeasonRepository $seasonRepository,
        EpisodeRepository $episodeRepository,
        CommentRepository $commentRepository,
        ValidatorInterface $validator
    ): Response {
        $season = $seasonRepository->findOneBy([
            "program" => $program,
            "number" => $season
        ]);

        if (is_null($season)) {
            throw new NotFoundHttpException();
        }

        $episode = $episodeRepository->findOneBy([
            "season" => $season,
            "number" => $episode,
            "slug" => $episodeSlug
        ]);

        if (is_null($episode)) {
            throw new NotFoundHttpException();
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $comment->setUser($this->getUser());
            $comment->setEpisode($episode);

            if ($validator->validate($comment)) {
                $commentRepository->save($comment, true);
            }
        }

        return $this->renderForm('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form
        ]);
    }

    #[Route('/new', methods: ["GET", "POST"], name: "new")]
    public function new(
        Request $request,
        ProgramRepository $programRepository,
        SluggerInterface $slugger,
        ValidatorInterface $validator,
        MailerInterface $mailer
    ): Response {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (trim($program->getTitle()) != "") {
                $program->setSlug($slugger->slug($program->getTitle()));
            }

            if ($validator->validate($program)) {
                $programRepository->save($program, true);

                $email = (new TemplatedEmail())->from($this->getParameter("mailer_from"))
                            ->to($this->getParameter("mailer_admin"))
                            ->subject('La série ' . $program->getTitle() . ' vient d\'être publiée !')
                            ->htmlTemplate("email/new_program.html.twig")
                            ->context([
                                "program" => $program
                            ]);

                $mailer->send($email);

                $this->addFlash("success", "La série " . $program->getTitle() . " à été ajouté.");
            }
            return $this->redirectToRoute('program_index');
        }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form
        ]);
    }
}
