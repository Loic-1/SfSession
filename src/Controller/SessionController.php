<?php

namespace App\Controller;

use App\Entity\Program;
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

        $nonIncluded = $sessionRepository->findNonProgrammes($session->getId());

        return $this->render('session/detailSession.html.twig', [
            'session' => $session,
            'nonIncluded' => $nonIncluded,
            'unattend' => $unattend
        ]);
    }

    /* MODIFICATION PUPIL */

    #[Route('/session/unlistPupil/{session}/{pupil}', name: 'unlist_pupil')]
    public function unregisterPupil(Session $session, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        // $pupil est target quand son id est égal à pupil_id
        // on supprime $pupil de la collection pupil de $session (aussi target en fonction de l'id)
        $session->removePupil($pupil);

        // on prépare la requête
        // $entityManager->persist($session);
        // on effectue la requête
        $entityManager->flush();

        // redirection vers session/detailSession.php, à la session 'id'
        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }

    #[Route('/session/listPupil/{session}/{pupil}', name: 'list_pupil')]
    public function registerPupil(Session $session, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        $session->addPupil($pupil);

        $entityManager->flush();

        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }

    /* MODIFICATION PROGRAM */

    #[Route('/session/unlistProgram/{session}/{program}', name: 'unlist_program')]
    public function unregisterProgram(Session $session, Program $program, EntityManagerInterface $entityManager): Response
    {
        $session->removeProgram2($program);

        $entityManager->flush();

        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }

    #[Route('/session/listProgram/{session}/{program}', name: 'list_program')]
    public function registerProgram(Session $session, Program $program, EntityManagerInterface $entityManager): Response
    {
        $session->addProgram($program);

        $entityManager->flush();

        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }
}
