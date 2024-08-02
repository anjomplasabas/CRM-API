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
}