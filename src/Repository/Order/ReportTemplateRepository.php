<?php

namespace App\Repository\Order;

use App\Entity\Order\ReportTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class ReportTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportTemplate::class);
    }

    public function save(ReportTemplate $reportTemplate): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($reportTemplate);
        $entityManager->flush();
    }

    public function findByPageId(int $pageId, int|null $page = 1, int|null $pageSize = 10, string|null $searchQuery = ''): mixed
    {
        $query = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.pageId = :pageId')
            ->setParameter('pageId', $pageId)
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if ($searchQuery) {
            $query = $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('r.fileName', ':searchQuery'),
                    $query->expr()->like('r.name', ':searchQuery'),
                )
            )->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $paginator = new Paginator($query->getQuery());

        $reportTemplates = array_map(fn ($reportTemplate): array => [
            'id' => $reportTemplate->getId(),
            'pageId' => $reportTemplate->getPageId(),
            'userId' => $reportTemplate->getUserId(),
            'name' => $reportTemplate->getName(),
            'fileName' => $reportTemplate->getFileName(),
            'headers' => implode(", ", $reportTemplate->getHeaders()),
            'parameters' => implode(", ", $reportTemplate->getParameters()),
            'createdAt' => $reportTemplate->getCreatedAt(),
            'updatedAt' => $reportTemplate->getUpdatedAt(),
        ], iterator_to_array($paginator));

        return [
            'data' => $reportTemplates,
            'total' => count($paginator),
            'totalPages' => ceil(count($paginator) / $pageSize),
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }    
}