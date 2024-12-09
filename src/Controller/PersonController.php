<?php

namespace App\Controller;

use App\Entity\Pupil;
use App\Entity\Session;
use App\Entity\Teacher;
use App\Repository\PupilRepository;
use App\Repository\SessionRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonController extends AbstractController
{
    #[Route('/person', name: 'app_person')]
    public function index(PupilRepository $pupilRepository, TeacherRepository $teacherRepository): Response
    {
        $pupils = $pupilRepository->findAll();
        $teachers = $teacherRepository->findAll();

        return $this->render('person/index.html.twig', [
            'pupils' => $pupils,
            'teachers' => $teachers
        ]);
    }

    #[Route('/person/{type}/{id}', name: 'detail_person')]
    public function detailPerson(string $type, int $id, EntityManagerInterface $entityManager, SessionRepository $sessionRepository): Response
    {
        // si la personne est un teacher
        if ($type === 'teacher') {
            $person = $entityManager->getRepository(Teacher::class)->find($id);
            $unattend = $sessionRepository->findNonSessionsTeacher($person->getId());
        }
        // si elle est un pupil
        elseif ($type === 'pupil') {
            $person = $entityManager->getRepository(Pupil::class)->find($id);
            $unattend = $sessionRepository->findNonSessionsPupil($person->getId());
        }

        return $this->render('person/detailPerson.html.twig', [
            'person' => $person,
            'unattend' => $unattend
        ]);
    }

    #[Route('/person/unlistSessionPupil/{type}/{pupil}/{session}', name: 'unlist_session_pupil')]
    public function unregisterSessionPupil(Session $session, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        $pupil->removeSession($session);

        $entityManager->flush();

        return $this->redirectToRoute('detail_person', ['type' => 'pupil', 'id' => $pupil->getId()]);
    }

    #[Route('/person/listSessionPupil/{type}/{pupil}/{session}', name: 'list_session_pupil')]
    public function registerSessionPupil(Session $session, Pupil $pupil, EntityManagerInterface $entityManager): Response
    {
        $pupil->addSession($session);

        $entityManager->flush();

        return $this->redirectToRoute('detail_person', ['type' => 'pupil', 'id' => $pupil->getId()]);
    }



    #[Route('/person/unlistSessionTeacher/{type}/{teacher}/{session}', name: 'unlist_session_teacher')]
    public function unregisterSessionTeacher(Session $session, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        $teacher->removeSession($session);

        $entityManager->flush();

        return $this->redirectToRoute('detail_person', ['type' => 'teacher', 'id' => $teacher->getId()]);
    }

    #[Route('/person/listSessionTeacher/{type}/{teacher}/{session}', name: 'list_session_teacher')]
    public function registerSessionTeacher(Session $session, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        $teacher->addSession($session);

        $entityManager->flush();

        return $this->redirectToRoute('detail_person', ['type' => 'teacher', 'id' => $teacher->getId()]);
    }
}
