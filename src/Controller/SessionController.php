<?php

namespace App\Controller;

use App\Entity\Pupil;
use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessionFuture = $sessionRepository->findFuture();

        $sessionsCurr = $sessionRepository->findCurr();

        $sessionsOld = $sessionRepository->findOld();

        return $this->render('session/index.html.twig', [
            'sessionsFuture' => $sessionFuture,
            'sessionsCurr' => $sessionsCurr,
            'sessionsOld' => $sessionsOld
        ]);
    }

    #[Route('/session/new', name: 'add_session')]
    public function addSession(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new Session;

        $form = $this->createForm(SessionFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData();

            $entityManager->persist($session);

            $entityManager->flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('session/addSession.html.twig', [
            'sessionForm' => $form
        ]);
    }

    #[Route('/session/remove/{id}', name: 'remove_session')]
    public function removeSession(Session $session, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }

    #[Route('/session/{id}', name: 'detail_session')]
    public function detailSession(SessionRepository $sessionRepository, Session $session): Response
    {
        $unattend = $sessionRepository->findNonInscrits($session->getId());

        return $this->render('session/detailSession.html.twig', [
            'session' => $session,
            'unattend' => $unattend
        ]);
    }

    #[Route('/session/unlist/{id}', name: 'unlist_session')]
    public function unregisterPupil(SessionRepository $sessionRepository, Pupil $pupil, Session $session) 
    {
        $sessionRepository->unregisterPupil($pupil->getId(), $session->getId());
        
        // return $this->redirectToRoute('detail_session');
        return $this->render('session/index.html.twig', [
            'session' => $session
        ]);
    }

    #[Route('/session/{id}', name: 'list_session')]
    public function registerPupil() 
    {
        return $this->redirectToRoute('detail_session');
    }
}
