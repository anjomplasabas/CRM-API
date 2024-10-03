<?php

namespace App\Controller\Webhook;

use App\Repository\User\PageRepository;
use App\Service\ThirdParty\FacebookService;
use App\Service\ThirdParty\FirebaseDatabase;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class FacebookController extends AbstractController
{
    // EAAQxUTQcWY8BOZC3Y0ZAEltEBTTtTLoNSIACZB8l37KeiOnNkz3R1ixXTzw1z6ZC243gtKzZAIDdrxIWUMZAqGZASg0OZCRh9ObGrlZC8k9DAnS0dTS9KCoZCBz5WFacohAtp8QxlubIfZCrMw1Wypc8Sl5wFxloZBcrDvDCxpES5J46AiYLYbJaPSWHXe9vMZA9jTM5rkwkWzbRwDZB7Q
    private const WEBHOOK_TOKEN = 'CGX2OCAFBV';

    public function __construct(
        private readonly FacebookService $facebookService,
        private readonly FirebaseDatabase $firebaseDatabase,
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
        try {
            $this->logger->info(' === GETTING CONVERSATIONS FROM FIREBASE === ');
            // Check existing conversations from firebase
            $conversationsResponse = $this->firebaseDatabase->retrieveConversationsByPageId($pageId);
        } catch (\Exception) {
            $this->logger->info(' === GETTING CONVERSATIONS FROM META === ');
            // Get page access token
            $pageAccessToken = $this->facebookService->getPageAccessToken($pageId, $userAccessToken);
            // Get list of conversations
            $conversations = $this->facebookService->getConversations($pageId, $pageAccessToken);
            // Prepare response of conversation
            $conversationsResponse = array_map(function ($conversation) use ($pageId) {
                $customer = array_filter($conversation['participants']['data'], function ($customer) use ($pageId) {
                    return $customer['id'] != $pageId;
                });

                return [
                    'conversationId' => $conversation['id'],
                    'customer' => current($customer),
                    'pageId' => $pageId,
                    'customerInitials' => substr($customer[0]['name'], 0, 1),
                    'lastMessage' => $conversation['messages']['data'][0]['message'] ?? 'Attachment',
                    'attachments' => isset($conversaton['messages']['data'][0]['attachments']) ? $conversation['messages']['data'][0]['attachments'] : [],
                    'lastMessageTime' => $conversation['messages']['data'][0]['created_time'],
                    'lastMessageTimestamp' => new DateTime($conversation['messages']['data'][0]['created_time'])
                ];
            }, $conversations['data']);
            usort($conversationsResponse, function ($a, $b) {
                return new DateTime($a['lastMessageTime']) <=> new DateTime($b['lastMessageTime']);
            });

            $this->firebaseDatabase->insertConversations($conversationsResponse);
        }

        return $this->json([
            'data' => $conversationsResponse
        ]);
    }

    #[Route('/facebook/messages', name: 'app_webhook_facebook_messages', methods: ['GET'])]
    public function getMessageList(Request $request): JsonResponse
    {
        $conversationId = $request->query->get('conversationId');
        $lastMessageId = $request->query->get('lastMessageId');
        $pageId = $request->query->get('pageId');
        $userAccessToken = $request->query->get('userAccessToken');
        
        $messagesResponse = $this->firebaseDatabase->retrieveMessagesByConversationId($conversationId);
        if ($messagesResponse && $lastMessageId == null) {
            $this->logger->info(' == RETURNING SINCE FIRST CALL FROM FIREBASE');

            return $this->json([
                'data' => $messagesResponse,
                'metadata' => []
            ]);
        }
        
        $this->logger->info(' == FETCHING ADDITIONAL MESSAGE FROM META');
        // Get page access token
        $pageAccessToken = $this->facebookService->getPageAccessToken($pageId, $userAccessToken);
        // Get list of messages
        $messages = $this->facebookService->getMessages(
            $conversationId,
            $pageAccessToken,
            null,
            $messagesResponse ? count($messagesResponse): null
        );
        $this->logger->info(' === RAW MESSAGES ==== ');
        $this->logger->info(json_encode($messages));
        // This means that a call from firebase database was done
        if ($lastMessageId) {
            $existingFromFirebase = array_filter($messagesResponse, function ($message) use ($lastMessageId) {
               return $message['messageId'] == $lastMessageId; 
            });
            // This means that the response from meta already exists in firebase
            // Override call with pagination
            if (!empty($existingFromFirebase)) {
                $messages = $this->facebookService->getMessages($conversationId, $pageAccessToken, $messages['paging']['cursors']['after']);
            }
        }
        // Prepare response of messages
        $messagesResponse = array_map(function ($message) use ($conversationId) {
            return [
                'from' => $message['from'],
                'to' => $message['to']['data'][0],
                'conversationId' => $conversationId,
                'message' => $message['message'],
                'attachments' => isset($message['attachments']) ? $message['attachments'] : [],
                'sticker' => isset($message['sticker']) ? $message['sticker'] : [],
                'messageId' => $message['id'],
                'createdTime' => $message['created_time'],
                'createdTimestamp' => new DateTime($message['created_time'])
            ];
        }, $messages['data']);
        usort($messagesResponse, function ($a, $b) {
            return new DateTime($a['createdTime']) <=> new DateTime($b['createdTime']);
        });
        $this->logger->info(' === MESSAGES ==== ');
        $this->logger->info(json_encode($messagesResponse));
        $this->firebaseDatabase->insertMessages($messagesResponse);

        return $this->json([
            'data' => $messagesResponse,
            'metadata' => $lastMessageId ?? isset($messages['paging']) ? $messages['paging'] : []
        ]);
    }

    #[Route('/facebook/messages', name: 'app_webhook_facebook_send_message', methods: ['POST'])]
    public function sendMessageRequest(Request $request): JsonResponse
    {
        $parameters = $request->toArray();
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
        // Get message details
        $messageDetails = $this->facebookService->getMessageDetails(
            $message['message_id'],
            $pageAccessToken
        );
        // Prepare firebase message
        $firebaseMessage = [
            'from' => $messageDetails['from'],
            'to' => $messageDetails['to']['data'][0],
            'conversationId' => $parameters['conversationId'],
            'message' => $messageDetails['message'],
            'attachments' => isset($messageDetails['attachments']) ? $messageDetails['attachments'] : [],
            'messageId' => $messageDetails['id'],
            'createdTime' => $messageDetails['created_time'],
            'createdTimestamp' => new DateTime($messageDetails['created_time'])
        ];
        $this->firebaseDatabase->insertMessages([$firebaseMessage]);
        // Prepare updated firebase conversation
        $firebaseConversation = [
            'conversationId' => $parameters['conversationId'],
            'customer' => $messageDetails['to']['data'][0],
            'pageId' => $parameters['pageId'],
            'customerInitials' => substr($messageDetails['to']['data'][0]['name'], 0, 1),
            'lastMessage' => $firebaseMessage['message'] ?? 'Attachment',
            'attachments' => isset($firebaseMessage['attachments']) ? $firebaseMessage['attachments'] : [],
            'lastMessageTime' => $firebaseMessage['createdTime'],
            'lastMessageTimestamp' => $firebaseMessage['createdTimestamp']
        ];
        $this->firebaseDatabase->insertConversations([$firebaseConversation]);

        return $this->json([
            'data' => $messageDetails
        ]);
    }

    #[Route('/facebook/messages/order-form', name: 'app_webhook_facebook_send_message_order_form', methods: ['POST'])]
    public function sendOrderFormRequest(Request $request): JsonResponse
    {
        $parameters = $request->toArray();
        $this->logger->info(json_encode($parameters));
        // Get page access token
        $pageAccessToken = $this->facebookService->getPageAccessToken(
            $parameters['pageId'],
            $parameters['userAccessToken']
        );
        // Send message request to facebook
        $message = $this->facebookService->sendMessageWithAttachment(
            $parameters['customerId'],
            $parameters['options'],
            $pageAccessToken,
            $parameters['pageId'],
            $this->getUser()?->getId()
        );

        $messageDetails = $this->facebookService->getMessageDetails(
            $message['message_id'],
            $pageAccessToken
        );
        // Prepare firebase message
        $firebaseMessage = [
            'from' => $messageDetails['from'],
            'to' => $messageDetails['to']['data'][0],
            'conversationId' => $parameters['conversationId'],
            'message' => $messageDetails['message'],
            'attachments' => isset($messageDetails['attachments']) ? $messageDetails['attachments'] : [],
            'messageId' => $messageDetails['id'],
            'createdTime' => $messageDetails['created_time'],
            'createdTimestamp' => new DateTime($messageDetails['created_time'])
        ];
        $this->firebaseDatabase->insertMessages([$firebaseMessage]);

        return $this->json([
            'data' => $messageDetails
        ]);
    }
}
