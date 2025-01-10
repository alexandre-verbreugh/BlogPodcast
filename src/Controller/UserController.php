<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


    #[Route('/user')]
    class UserController extends AbstractController
    {
        #[Route('/', name: 'app_user_index')]
        public function index(EntityManagerInterface $entityManager): Response
        {
            $user = $entityManager->getRepository(User::class)->findAll();
            return $this->render('user/index.html.twig', [
                'users' => $user,
            ]);
        }

        #[Route('/edit/{id}', name: 'app_user_edit')]
        public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
        {
            if ($request->isMethod('POST')) {
                $role = $request->request->get('role');
                
                if ($role === 'ROLE_ADMIN') {
                    $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
                } else {
                    $user->setRoles(['ROLE_USER']);
                }

                $entityManager->flush();
                $this->addFlash('success', 'Les rôles ont été modifiés avec succès');
                
                return $this->redirectToRoute('app_user_index');
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user
            ]);
        }

        #[Route('/delete/{id}', name: 'app_user_delete')]
        public function delete(User $user, EntityManagerInterface $entityManager): Response
        {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès');

            return $this->redirectToRoute('app_user_index');
        }
    }




