<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Form\CommentType;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/episode')]
class EpisodeController extends AbstractController
{
    #[Route('/', name: 'app_episode_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EpisodeRepository $episodeRepository,
        MailerInterface $mailer,
        SluggerInterface $slugger,
        ValidatorInterface $validator
    ): Response {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (trim($episode->getTitle()) != "") {
                $episode->setSlug($slugger->slug($episode->getTitle()));
            }

            if ($validator->validate($episode)) {
                $episodeRepository->save($episode, true);

                $email = (new TemplatedEmail())->from($this->getParameter("mailer_from"))
                        ->to($this->getParameter("mailer_admin"))
                        ->subject('L\'episode ' . $episode->getTitle() . ' vient d\'être publiée !')
                        ->htmlTemplate("email/new_episode.html.twig")
                        ->context([
                            "episode" => $episode
                        ]);

                $mailer->send($email);

                $this->addFlash(
                    'success',
                    'L\'episode ' . $episode->getNumber() . ' de la saison ' . $episode->getSeason()->getNumber() .
                    ' de ' . $episode->getSeason()->getProgram()->getTitle() . ' (' . $episode->getTitle() .
                    ') à été ajouté'
                );
            }

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_show', methods: ['GET'])]
    public function show(Episode $episode): Response
    {
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeRepository->save($episode, true);

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $episode->getId(), $request->request->get('_token'))) {
            $episodeRepository->remove($episode, true);
            $this->addFlash('danger', 'L\'episode ' . $episode->getTitle() . ' à été supprimé');
        }

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
