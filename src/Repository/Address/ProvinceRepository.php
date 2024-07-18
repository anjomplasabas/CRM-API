<?php

namespace App\Repository\Address;

use App\Entity\Address\Province;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Province>
 */
class ProvinceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Province::class);
    }

    public function save(Province $province): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($province);
        $entityManager->flush();
    }
}
