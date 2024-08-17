<?php

namespace App\Event\User;

use Symfony\Contracts\EventDispatcher\Event;

class PageEvent extends Event
{
    public const NAME = 'pages.event';

    private $pagesData;
    private $userId;

    public function __construct(array $pagesData, int $userId)
    {
        $this->pagesData = $pagesData;
        $this->userId = $userId;
    }

    public function getPagesData(): array
    {
        return $this->pagesData;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}