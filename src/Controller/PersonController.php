<?php

namespace App\Controller;

use App\Entity\Pupil;
use App\Entity\Teacher;
use App\Repository\PupilRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    public function detailTeacher(string $type, int $id, EntityManagerInterface $entityManager): Response
    {
        // si la personne est un teacher
        if ($type === 'teacher') {
            $person = $entityManager->getRepository(Teacher::class)->find($id);
        }
        // si elle est un pupil
        elseif ($type === 'pupil') {
            $person = $entityManager->getRepository(Pupil::class)->find($id);
        }

        return $this->render('person/detailPerson.html.twig', [
            'person' => $person
        ]);
    }
}
