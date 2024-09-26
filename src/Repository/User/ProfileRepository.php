<?php

namespace App\Repository\User;

use App\Entity\User\Profile;
use App\DTO\Request\User\ProfileDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @extends ServiceEntityRepository<Profile>
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function save(Profile $profile): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($profile);
        $entityManager->flush();
    }

    public function findProfileByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.userId, p.firstName, p.lastName, p.email, p.mobileNumber, p.metadata')
            ->where('p.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
        
    }

    public function updateProfile(int $userId, ProfileDTO $profileDTO): Profile|null
    {
        $profile = $this->findOneBy(['userId' => $userId]);
        if (!$profile) {
            return null;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach (get_object_vars($profileDTO) as $field => $value) {
            if (!$propertyAccessor->isReadable($profile, $field)) {
                continue;
            }
            $propertyAccessor->setValue($profile, $field, $value);
        }

        $entityManager = $this->getEntityManager();
        $entityManager->persist($profile);
        $entityManager->flush();

        return $profile;
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
