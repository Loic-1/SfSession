<?php

namespace App\Repository;

use App\Entity\Session;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findOld(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere(':curr > s.end_date')
            ->setParameter('curr', new DateTime('now'))
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFuture()
    {
        return $this->createQueryBuilder('s')
            ->andWhere(':curr < s.start_date')
            ->setParameter('curr', new DateTime('now'))
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCurr()
    {
        return $this->createQueryBuilder('s')
            ->andWhere(':curr BETWEEN s.start_date AND s.end_date')
            ->setParameter('curr', new DateTime('now'))
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /** Afficher les stagiaires non inscrits dans une session */
    public function findNonInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('p')
            ->from('App\Entity\Pupil', 'p')
            ->leftJoin('p.session', 's')
            ->where('s.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('pu')
            ->from('App\Entity\Pupil', 'pu')
            ->where($sub->expr()->notIn('pu.id', $qb->getDQL()))
            // requête paramétrée (on définit :id pour $qb)
            ->setParameter('id', $session_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('pu.name', 'ASC');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

    /** Afficher les programmes non inclus dans une session */
    public function findNonProgrammes($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les programmes d'une session dont l'id est passé en paramètre
        $qb->select('pr')
            ->from('App\Entity\Program', 'pr')
            ->leftJoin('pr.session', 'se')
            ->where('se.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('pro')
            ->from('App\Entity\Program', 'pro')
            ->where($sub->expr()->notIn('pro.id', $qb->getDQL()))
            // requête paramétrée (on définit :id pour $qb)
            ->setParameter('id', $session_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('pro.duration', 'ASC');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }


    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
