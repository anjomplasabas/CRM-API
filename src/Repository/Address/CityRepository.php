<?php

namespace App\Repository\Address;

use App\Entity\Address\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function save(City $city): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($city);
        $entityManager->flush();
    }
}
