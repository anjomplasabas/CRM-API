<?php

namespace App\Service\User;

use App\Entity\User\Page;
use App\Repository\User\PageRepository;
use Doctrine\ORM\EntityManagerInterface;

class PageService
{
    private $pageRepository;
    private $entityManager;

    public function __construct(PageRepository $pageRepository, EntityManagerInterface $entityManager)
    {
        $this->pageRepository = $pageRepository;
        $this->entityManager = $entityManager;
    }

    public function insertOrUpdatePages(array $data, int $userId): void
    {
        $batchSize = 20;

        foreach ($data as $index => $row) {
            $page = $this->pageRepository->findOneBy([
                'userId' => $userId,
                'facebookPageId' => $row['id']
            ]);

            if (!$page) {
                $page = new Page();
                $page->setUserId($userId);
                $page->setFacebookPageId($row['id']);
            }

            $page->setName($row['name']);
            $page->setFacebookPageAccessToken($row['access_token']);
            $page->setMetadata($row);
            $page->setCreatedAt(new \DateTime());
            $page->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($page);

            if (($index % $batchSize) === 0) {
                $this->flushAndClear();
            }
        }

        $this->flushAndClear();
    }

    private function flushAndClear(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}