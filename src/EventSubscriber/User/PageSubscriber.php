<?php

namespace App\EventSubscriber\User;

use App\Event\User\PageEvent;
use App\Service\User\PageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageSubscriber implements EventSubscriberInterface
{
    private $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public static function getSubscribedEvents()
    {
        return [
            PageEvent::NAME => 'onPageInsert',
        ];
    }

    public function onPageInsert(PageEvent $event)
    {
        $this->pageService->insertOrUpdatePages($event->getPagesData(), $event->getUserId());
    }
}