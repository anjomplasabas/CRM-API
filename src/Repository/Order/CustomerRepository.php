<?php

namespace App\Repository\Order;

use App\Entity\Order\Customer;
use App\DTO\Request\Order\CustomerDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function save(Customer $customer): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($customer);
        $entityManager->flush();
    }

    public function findByPageId(int $pageId, int|null $page = 1, int|null $pageSize = 10, string|null $searchQuery = ''): mixed
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.pageId = :pageId')
            ->setParameter('pageId', $pageId)
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if ($searchQuery) {
            $query = $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('p.fullName', ':searchQuery'),
                    $query->expr()->like('p.email', ':searchQuery'),
                    $query->expr()->like('p.region', ':searchQuery'),
                    $query->expr()->like('p.province', ':searchQuery'),
                    $query->expr()->like('p.city', ':searchQuery'),
                    $query->expr()->like('p.barangay', ':searchQuery'),
                )
            )->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $paginator = new Paginator($query->getQuery());

        $customers = array_map(fn ($customer): array => [
            'id' => $customer->getId(),
            'pageId' => $customer->getPageId(),
            'facebookProfileId' => $customer->getFacebookProfileId(),
            'fullName' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'mobileNumber' => $customer->getMobileNumber(),
            'addressLine' => $customer->getAddressLine(),
            'region' => $customer->getRegion(),
            'province' => $customer->getProvince(),
            'city' => $customer->getCity(),
            'barangay' => $customer->getBarangay(),
            'zipCode' => $customer->getZipCode(),
        ], iterator_to_array($paginator));

        return [
            'data' => $customers,
            'total' => count($paginator),
            'totalPages' => ceil(count($paginator) / $pageSize),
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    //    /**
    //     * @return Profile[] Returns an array of Profile objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Profile
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
