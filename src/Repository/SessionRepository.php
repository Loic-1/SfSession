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

    // désenregistrer un stagiaire d'une session
    // public function unregisterPupil($pupil_id, $session_id) 
    // {
    //     $em = $this->getEntityManager();
    //     $qb = $em->createQueryBuilder();

    //     $qb->delete('App\Entity\Pupil', 'p')
        
    //         // on accède à la Collection session de Pupil et on lui attribue l'alias 's'
    //         // ->leftJoin('p.session', 's')
    //         // où pupil.id = $pupil_id
    //         ->where('p.id = :pupil_id')

    //         ->andWhere('s.id = :session_id')

    //         // on définit ':id'
    //         ->setParameter('pupil_id', $pupil_id)
    //         ->setParameter('session_id', $session_id)
    //     ;

    //     // on prépare la requête
    //     $query = $qb->getQuery();
    //     // on renvoie le résultat
    //     return $query->getResult();
    // }



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
