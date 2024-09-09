<?php

namespace App\Controller\Webhook;

use App\Repository\User\PageRepository;
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
        private readonly LoggerInterface $logger,
        private readonly PageRepository $pageRepository
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
        $this->logger->info(' === FROM FACEBOOK CALLBACK === ');
        $this->logger->info(json_encode($request->toArray()));
        return $this->json(
            $request->toArray()
        );
    }

    #[Route('/facebook/conversations', name: 'app_webhook_facebook_conversations', methods: ['GET'])]
    public function getConversationList(Request $request): JsonResponse
    {
        $pageId = $request->query->get('pageId');
        $userAccessToken = $request->query->get('userAccessToken');
        // Get page access token
        $pageAccessToken = $this->facebookService->getPageAccessToken($pageId, $userAccessToken);
        // Get list of conversations
        $conversations = $this->facebookService->getConversations($pageId, $pageAccessToken);
        // Prepare response of conversation
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
                'attachments' => isset($conversaton['messages']['data'][0]['attachments']) ? $conversation['messages']['data'][0]['attachments'] : [],
                'lastMessageTime' => $conversation['messages']['data'][0]['created_time']
            ];
        }, $conversations['data']);

        return $this->json([
            'data' => $response,
            'metadata' => $conversations['paging']
        ]);
    }

    #[Route('/facebook/messages', name: 'app_webhook_facebook_messages', methods: ['GET'])]
    public function getMessageList(Request $request): JsonResponse
    {
        $conversationId = $request->query->get('conversationId');
        $userAccessToken = $request->query->get('userAccessToken');
        $pageId = $request->query->get('pageId');
        $paginate = $request->query->has('paginate') ? $request->query->get('paginate') : '';
        // Get page access token
        $pageAccessToken = $this->facebookService->getPageAccessToken($pageId, $userAccessToken);
        // Get list of messages
        $messages = $this->facebookService->getMessages($conversationId, $pageAccessToken, $paginate);
        // Prepare response of messages
        $response = array_map(function($message) {
            return [
                'from' => $message['from'],
                'to' => $message['to']['data'][0],
                'message' => $message['message'],
                'attachments' => isset($message['attachments']) ? $message['attachments'] : [],
                'messageId' => $message['id'],
                'createdTime' => $message['created_time']
            ];
        }, $messages['data']);
        
        return $this->json([
            'data' => $response,
            'metadata' => array_merge(
                isset($messages['paging']) ? $messages['paging'] : [],
                ['conversationId' => $conversationId]
            )
        ]);
    }

    #[Route('/facebook/messages', name: 'app_webhook_facebook_send_message', methods: ['POST'])]
    public function sendMessageRequest(Request $request): JsonResponse
    {
        $parameters = $request->toArray();
        $this->logger->info(json_encode($parameters));
        // Get page access token
        $pageAccessToken = $this->facebookService->getPageAccessToken(
            $parameters['pageId'],
            $parameters['userAccessToken']
        );
        // Send message request to facebook
        $message = $this->facebookService->sendMessage(
            $parameters['customerId'],
            $parameters['options'],
            $pageAccessToken,
            $parameters['pageId']
        );

        $messageDetails = $this->facebookService->getMessageDetails(
            $message['message_id'],
            $pageAccessToken
        );

        return $this->json([
            'data' => $messageDetails
        ]);
    }
}
