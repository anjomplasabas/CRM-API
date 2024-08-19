<?php

namespace App\Event\User;

use Symfony\Contracts\EventDispatcher\Event;

final class PageEvent extends Event
{
    public const NAME = 'pages.event';
    
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