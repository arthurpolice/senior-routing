<?php

namespace App\Repository;

use App\Entity\ServiceProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceProvider>
 */
class ServiceProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceProvider::class);
    }

    public function findDeliveriesWithClientsForDate(
        ServiceProvider $provider,
        \DateTimeImmutable $date,
        int $windowDays
    ): array {
        $windowStart = (clone $date)->setTime(0, 0);
        $windowEnd   = (clone $date)->modify(\sprintf('+%d day', $windowDays))->setTime(23, 59, 59);
    
        return $this->createQueryBuilder('sp')
            ->select('sp', 'o', 'client')
            ->join('sp.orders', 'o')
            ->join('o.client', 'client')
            ->andWhere('sp = :provider')
            ->andWhere('o.date BETWEEN :start AND :end')
            ->setParameter('provider', $provider)
            ->setParameter('start', $windowStart)
            ->setParameter('end', $windowEnd)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return ServiceProvider[] Returns an array of ServiceProvider objects
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

    //    public function findOneBySomeField($value): ?ServiceProvider
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
