<?php

namespace App\Controller;

use App\Entity\Commantaire;
use App\Entity\Episode;
use App\Form\CommantaireType;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        // Si le formulaire n'est pas valide, prépare les données pour afficher la page d'accueil
        $episodes = $episodeRepository->findAll();
        $commentForms = [];
        foreach ($episodes as $ep) {
            $commentForms[$ep->getId()] = $this->createForm(CommantaireType::class)->createView();
        }
        $commentForms[$episode->getId()] = $form->createView(); // Inclure le formulaire soumis avec ses erreurs

        return $this->render('home/index.html.twig', [
            'episodes' => $episodes,
            'commentForms' => $commentForms,
            'errors' => $form->getErrors(true, false), // Optionnel : pour déboguer
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


}
