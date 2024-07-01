<?php

namespace App\Repository\Organization;

use App\Entity\Organization\OrganizationMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationMember>
 */
class OrganizationMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationMember::class);
    }

    public function findById(int $memberId): ?array
    {
        $member = $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->leftJoin('u.profile', 'p')
            ->select('o, u, p')
            ->where('o.id = :memberId')
            ->setParameter('memberId', $memberId)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'id' => $member->getId(),
            'userId' => $member->getUserId(),
            'roles' => $member->getUser()->getRoles(),
            'authId' => $member->getUser()->getAuthId(),
            'firstName' => $member->getUser()->getProfile()->getFirstName(),
            'lastName' => $member->getUser()->getProfile()->getLastName(),
            'email' => $member->getUser()->getProfile()->getEmail(),
            'mobileNumber' => $member->getUser()->getProfile()->getMobileNumber(),
            'memberStatus' => $member->getStatus(),
            'userStatus' => $member->getUser()->getStatus(),
            'createdAt' => $member->getCreatedAt(),
            'updatedAt' => $member->getUpdatedAt(),
        ];
    }

    public function findByOrganizationId(int $organizationId, int|null $page = 1, int|null $pageSize = 10, string|null $searchQuery = ''): mixed
    {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->leftJoin('u.profile', 'p')
            ->select('o, u, p')
            ->where('o.organizationId = :organizationId')
            ->setParameter('organizationId', $organizationId)
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        if ($searchQuery) {
            $query = $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('p.firstName', ':searchQuery'),
                    $query->expr()->like('p.lastName', ':searchQuery'),
                    $query->expr()->like('p.email', ':searchQuery')
                )
            )->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $paginator = new Paginator($query->getQuery());

        $members = array_map(fn ($member): array => [
            'id' => $member->getId(),
            'userId' => $member->getUserId(),
            'roles' => $member->getUser()->getRoles(),
            'authId' => $member->getUser()->getAuthId(),
            'firstName' => $member->getUser()->getProfile()->getFirstName(),
            'lastName' => $member->getUser()->getProfile()->getLastName(),
            'email' => $member->getUser()->getProfile()->getEmail(),
            'mobileNumber' => $member->getUser()->getProfile()->getMobileNumber(),
            'memberStatus' => $member->getStatus(),
            'userStatus' => $member->getUser()->getStatus(),
            'createdAt' => $member->getCreatedAt(),
            'updatedAt' => $member->getUpdatedAt(),
        ], iterator_to_array($paginator));

        return [
            'data' => $members,
            'total' => count($paginator),
            'totalPages' => ceil(count($paginator) / $pageSize),
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public function save(OrganizationMember $organizationMember): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($organizationMember);
        $entityManager->flush();
    }
}
