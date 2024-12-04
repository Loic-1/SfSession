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

    #[Route('/session/unlist/{session_id}/{pupil_id}', name: 'unlist_pupil')]
    public function unregisterPupil(Session $session, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        // $pupil est target quand son id est égal à pupil_id
        // on supprime $pupil de la collection pupil de $session (aussi target en fonction de l'id)
        $session->removePupil($pupil);

        // on prépare le changement
        $entityManager->persist($session);
        // on push
        $entityManager->flush();

        // redirection vers session/detailSession.php, à la session 'id'
        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }

    #[Route('/session/{session_id}/{pupil_id}', name: 'list_pupil')]
    public function registerPupil(Session $session, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }
}
