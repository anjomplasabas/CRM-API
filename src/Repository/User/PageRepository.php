<?php

namespace App\Repository\User;

use App\Entity\User\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function findByUserId(int $userId, int|null $page = 1, int|null $pageSize = 10, string|null $searchQuery = ''): mixed
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.userId = :userId')
            ->setParameter('userId', $userId)
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if ($searchQuery) {
            $query = $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('p.name', ':searchQuery'),
                )
            )->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $paginator = new Paginator($query->getQuery());

        $pages = array_map(fn ($page): array => [
            'id' => $page->getId(),
            'name' => $page->getName(),
            'createdAt' => $page->getCreatedAt(),
            'updatedAt' => $page->getUpdatedAt(),
        ], iterator_to_array($paginator));

        return [
            'data' => $pages,
            'total' => count($paginator),
            'totalPages' => ceil(count($paginator) / $pageSize),
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }    
}
