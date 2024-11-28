<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionFormType;
use App\Repository\SessionRepository;
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
        $sessions = $sessionRepository->findAll();

        // var_dump($sessions);
        // die;

        $sessionFuture = $sessionRepository->findFuture();

        $sessionsCurr = $sessionRepository->findCurr();

        $sessionsOld = $sessionRepository->findOld();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
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

    #[Route('/session/{id}', name: 'detail_session')]
    public function detailSession(Session $session): Response {
        return $this->render('session/detailSession.html.twig', [
            'session' => $session
        ]);
    }
}
