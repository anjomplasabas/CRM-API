<?php

namespace App\Repository\User;

use App\Entity\User\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function save(Page $page): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($page);
        $entityManager->flush();
    }
}
