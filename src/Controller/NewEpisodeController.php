<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewEpisodeController extends AbstractController
{
    #[Route('/new', name: 'app_new', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            /** @var UploadedFile $audioFile */
            $audioFile = $form->get('audioFile')->getData();
            $coverImageFile = $form->get('coverImageFile')->getData();

            // Traitement du fichier audio
            if ($audioFile) {
                try {
                    $originalFilename = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$audioFile->guessExtension();
                    
                    // Création du dossier s'il n'existe pas
                    $targetDirectory = $this->getParameter('kernel.project_dir').'/public/episodes';
                    if (!file_exists($targetDirectory)) {
                        mkdir($targetDirectory, 0777, true);
                    }

                    $audioFile->move($targetDirectory, $newFilename);
                    $episode->setAudio('/episodes/'.$newFilename);
                    
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier audio');
                    return $this->redirectToRoute('app_new');
                }
            }

            // Traitement de l'image de couverture
            if ($coverImageFile) {
                try {
                    $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImageFile->guessExtension();
                    
                    // Création du dossier s'il n'existe pas
                    $targetDirectory = $this->getParameter('kernel.project_dir').'/public/covers';
                    if (!file_exists($targetDirectory)) {
                        mkdir($targetDirectory, 0777, true);
                    }

                    $coverImageFile->move($targetDirectory, $newFilename);
                    $episode->setCoverImage('/covers/'.$newFilename);
                    
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image de couverture');
                    return $this->redirectToRoute('app_new');
                }
            }

            try {
                $entityManager->persist($episode);
                $entityManager->flush();
                
                $this->addFlash('success', 'Episode créé avec succès !');
                
                // Redirection avec statut HTTP explicite
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la sauvegarde de l\'épisode');
                return $this->redirectToRoute('app_new');
            }
        }

        return $this->render('new_episode/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit/{id}', name: 'app_edit', methods: ['GET', 'POST'])]
    public function edit(Episode $episode, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $audioFile */
            $audioFile = $form->get('audioFile')->getData();
            $coverImageFile = $form->get('coverImageFile')->getData();

            // Gestion du fichier audio
            if ($audioFile) {
                // Supprime l'ancien fichier si existant
                if ($episode->getAudio() && file_exists($this->getParameter('kernel.project_dir').'/public'.$episode->getAudio())) {
                    unlink($this->getParameter('kernel.project_dir').'/public'.$episode->getAudio());
                }
            
                try {
                    $originalFilename = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$audioFile->guessExtension();
            
                    $targetDirectory = $this->getParameter('kernel.project_dir').'/public/episodes';
                    if (!file_exists($targetDirectory)) {
                        mkdir($targetDirectory, 0777, true);
                    }
            
                    $audioFile->move($targetDirectory, $newFilename);
                    $episode->setAudio('/episodes/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier audio');
                }
            }

            // Gestion de l'image de couverture
            if ($coverImageFile) {
                // Supprime l'ancienne image si existante
                if ($episode->getCoverImage() && file_exists($this->getParameter('kernel.project_dir').'/public'.$episode->getCoverImage())) {
                    unlink($this->getParameter('kernel.project_dir').'/public'.$episode->getCoverImage());
                }
            
                try {
                    $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImageFile->guessExtension();
            
                    $targetDirectory = $this->getParameter('kernel.project_dir').'/public/covers';
                    if (!file_exists($targetDirectory)) {
                        mkdir($targetDirectory, 0777, true);
                    }
            
                    $coverImageFile->move($targetDirectory, $newFilename);
                    $episode->setCoverImage('/covers/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image de couverture');
                }
            }
            

            $entityManager->flush();
            
            $this->addFlash('success', 'Épisode mis à jour avec succès !');

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('new_episode/index.html.twig', [
            'form' => $form->createView(),
            'episode' => $episode,
            'editMode' => true, // Pour différencier création/modification dans le template
        ]);
    }


}