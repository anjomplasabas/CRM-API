<?php

namespace App\Repository\Order;

use App\Entity\Order\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Organization>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($order);
        $entityManager->flush();
    }

    public function findByPageId(int $pageId, int|null $page = 1, int|null $pageSize = 10, string|null $searchQuery = ''): mixed
    {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.customer', 'c')
            ->select('o, c')
            ->where('o.pageId = :pageId')
            ->setParameter('pageId', $pageId)
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if ($searchQuery) {
            $query = $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('o.referenceNumber', ':searchQuery'),
                    $query->expr()->like('c.fullName', ':searchQuery'),
                    $query->expr()->like('o.totalAmount', ':searchQuery'),
                    $query->expr()->like('o.grandTotalAmount', ':searchQuery'),
                    $query->expr()->like('o.status', ':searchQuery'),
                )
            )->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $paginator = new Paginator($query->getQuery());

        $orders = array_map(fn ($order): array => [
            'id' => $order->getId(),
            'pageId' => $order->getPageId(),
            'customerId' => $order->getCustomerId(),
            'fullName' => $order->getCustomer()->getFullName(),
            'totalAmount' => $order->getTotalAmount(),
            'grandTotalAmount' => $order->getGrandTotalAmount(),
            'referenceNumber' => $order->getReferenceNumber(),
            'status' => $order->getStatus(),
            'metadata' => $order->getMetadata(),
            'createdAt' => $order->getCreatedAt(),
            'updatedAt' => $order->getUpdatedAt(),
        ], iterator_to_array($paginator));

        return [
            'data' => $orders,
            'total' => count($paginator),
            'totalPages' => ceil(count($paginator) / $pageSize),
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public function findByPageIdAndDateRange($pageId, string $startDate, string $endDate)
    {
        $startDateTime = \DateTime::createFromFormat('Y-m-d', $startDate)->setTime(0, 0, 0);
        $endDateTime = \DateTime::createFromFormat('Y-m-d', $endDate)->setTime(23, 59, 59);

        $orders = $this->createQueryBuilder('o')
            ->where('o.pageId = :pageId')
            ->andWhere('o.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('pageId', $pageId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $orders->getQuery()->getResult();
    }    

    //    /**
    //     * @return Organization[] Returns an array of Organization objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Organization
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
