<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortiesRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Sortie::class);
        $this->security = $security;
    }

    public function findByFilters(array $filters): array
    {
        $user = $this->security->getUser();
        $qb = $this->createQueryBuilder('s');

        if (!empty($filters['nomSortie'])) {
            $qb->andWhere('s.category = :category')
                ->setParameter('nomSortie', $filters['nomSortie']);
        }

        if (!empty($filters['site'])) {
            $qb->andWhere('s.siteOrganisateur = :site')
                ->setParameter('site', $filters['site']);
        }

        if (!empty($filters['etat'])) {
            $qb->andWhere('s.etat <= :etat')
                ->setParameter('etat', $filters['etat']);
        }

        if (!empty($filters['estOrganiqateur'])) {
            $qb->andWhere('s.organiqateur = :user')
                ->setParameter('user', $user->getId());
        }

//        if (!empty($filters['estInscrit'])) {
//            $qb->andWhere('s.stock > 0')
//                ->setParameter('user', $user->getId());
//        }
//
//        if (!empty($filters['estPasInscrit'])) {
//            $qb->andWhere('s.stock > 0')
//                ->setParameter('user', $user->getId());
//        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
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

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
