<?php

namespace App\Repository\Address;

use App\Entity\Address\Barangay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Barangay>
 */
class BarangayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Barangay::class);
    }

    public function save(Barangay $barangay): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($barangay);
        $entityManager->flush();
    }
}
