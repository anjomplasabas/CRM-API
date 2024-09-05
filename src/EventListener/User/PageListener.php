<?php

namespace App\EventListener\User;

use App\Event\User\PageEvent;
use App\Service\User\PageService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class PageListener
{
    public function __construct(
        private PageService $pageService
    ) {
        $this->pageService = $pageService;
    }

    public function __invoke(PageEvent $event): void
    {
        $this->pageService->insertOrUpdatePages($event->getPagesData(), $event->getUserId());
    }

}