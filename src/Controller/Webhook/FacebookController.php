<?php

namespace App\Controller\Webhook;

use App\Service\ThirdParty\FacebookService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class FacebookController extends AbstractController
{
    const WEBHOOK_TOKEN = 'CGX2OCAFBV';

    public function __construct(
        private readonly FacebookService $facebookService,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/callback/facebook-verify', name: 'app_webhook_facebook_verify', methods: ['GET'])]
    public function verify(Request $request): JsonResponse
    {
        $this->logger->info($request);
        if (!$request->query->has('hub_mode')) {
            return $this->json([
                'message' => 'Invalid callback request.'
            ], 400);
        }
        if ($request->query->get('hub_verify_token') != self::WEBHOOK_TOKEN) {
            return $this->json([
                'message' => 'Invalid callback token.'
            ], 401);
        }
        return $this->json(
            intval($request->query->get('hub_challenge'))
        );
    }

    #[Route('/callback/facebook-verify', name: 'app_webhook_facebook_subscribe', methods: ['POST'])]
    public function subscribe(Request $request)
    {
        $this->logger->info(json_encode($request->toArray()));
        return $this->json(
            $request->toArray()
        );
    }

    #[Route('/facebook/conversations', name: 'app_webhook_facebook_conversations', methods: ['GET'])]
    public function getConversationList(Request $request): JsonResponse
    {
        $pageId = $request->query->get('pageId');
        $pageToken = 'EAAcd4WZCBKHIBO3ZBtlZBhpNaKpkJpZBmxEYPUKj8G8aTtXiDi3DeMESXoPWZA1RqvuHf2pf3bKud3cuR7ckEOGDRXCvg2XHCMDPU0H4aZCPJjQlVYGQjNxnehRxZC4gHAvLOEums3oyxCVYOnlcanruw6j4DunivOpIKSWZASQxXMtG1nk0HHeEBM1byByZAhZBXrF5iXR4EZCmXaa5ZAydom7kWHJZB3EKVgWUZD';
        // Add logic to fetch page from DB and use accessToken
        $conversations = $this->facebookService->getConversations($pageId, $pageToken);
        $response = array_map(function($conversation) use ($pageId) {
            $customer = array_filter($conversation['participants']['data'], function ($customer) use ($pageId) {
                return $customer['id'] != $pageId;
            });

            return [
                'conversationId' => $conversation['id'],
                'customer' => current($customer),
                'customerInitials' => substr($customer[0]['name'], 0, 1),
                'messageCount' => $conversation['message_count'],
                'lastMessage' => $conversation['messages']['data'][0]['message'],
                'lastMessageTime' => $conversation['messages']['data'][0]['created_time']
            ];
        }, $conversations['data']);

        return $this->json([
            'data' => $response
        ]);
    }
}
