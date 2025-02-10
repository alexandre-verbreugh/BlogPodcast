<?php

namespace App\Controller;

use App\Entity\Commantaire;
use App\Entity\Episode;
use App\Form\CommantaireType;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EpisodeRepository $episodeRepository): Response
    {
        $episodes = $episodeRepository->findBy([], ['createdAt' => 'DESC']);

        // Préparer un formulaire pour chaque épisode
        $commentForms = [];
        foreach ($episodes as $episode) {
            $commentForm = $this->createForm(CommantaireType::class, null, [
                'action' => $this->generateUrl('app_comment_add', ['id' => $episode->getId()]),
            ]);
            $commentForms[$episode->getId()] = $commentForm->createView();
        }

        return $this->render('home/index.html.twig', [
            'episodes' => $episodes,
            'commentForms' => $commentForms,
        ]);
    }

    #[Route('/comment/add/{id}', name: 'app_comment_add', methods: ['POST'])]
    public function addComment(
        Request $request, 
        Episode $episode, 
        EntityManagerInterface $entityManager, 
        EpisodeRepository $episodeRepository
    ): Response {
        $commantaire = new Commantaire();
        $commantaire->setEpisode($episode);

        $form = $this->createForm(CommantaireType::class, $commantaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commantaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        // Préparation des données pour afficher la page d'accueil
        $episodes = $episodeRepository->findAll();
        $commentForms = [];
        foreach ($episodes as $ep) {
            $commentForms[$ep->getId()] = $this->createForm(CommantaireType::class)->createView();
        }
        $commentForms[$episode->getId()] = $form->createView();

        return $this->render('home/index.html.twig', [
            'episodes' => $episodes,
            'commentForms' => $commentForms,
            'errors' => $form->getErrors(true, false),
        ]);
    }

    #[Route('/episode/{id}', name: 'app_episode_show')]
    public function show(Episode $episode): Response
    {
        $commentForm = $this->createForm(CommantaireType::class, null, [
            'action' => $this->generateUrl('app_comment_add', ['id' => $episode->getId()]),
        ]);
        
        return $this->render('home/show.html.twig', [
            'episode' => $episode,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/episode/{id}/view', name: 'app_episode_view', methods: ['POST'])]
    public function incrementView(Episode $episode, EntityManagerInterface $entityManager): JsonResponse
    {
        $episode->incrementViewCount();
        $entityManager->flush();

        return new JsonResponse(['views' => $episode->getViewCount()]);
    }

    #[Route('/episode/{id}/like', name: 'app_episode_like', methods: ['POST'])]
    public function incrementLike(Episode $episode, EntityManagerInterface $entityManager): JsonResponse
    {
        $episode->incrementLikeCount();
        $entityManager->flush();

        return new JsonResponse(['likes' => $episode->getLikeCount()]);
    }
}
