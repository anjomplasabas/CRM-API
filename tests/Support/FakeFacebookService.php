<?php

namespace App\Tests\Support;

use App\Service\ThirdParty\FacebookService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;

class FakeFacebookService extends FacebookService
{
    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct(new MockHttpClient(), $logger ?? new NullLogger(), 'https://orderform.test');
    }

    public function getConversations(string $pageId, string $pageAccessToken): array
    {
        return [
            'data' => [
                [
                    'id' => 't_1',
                    'name' => 'Juan Dela Cruz',
                    'message_count' => 1,
                    'participants' => ['data' => [['id' => 'cust_1', 'name' => 'Juan Dela Cruz']]],
                    'messages' => ['data' => [['from' => ['id' => 'cust_1'], 'message' => 'Hello', 'created_time' => '2026-01-01T00:00:00+0000']]],
                ],
            ],
        ];
    }

    public function getMessages(
        string $conversationId,
        string $pageAccessToken,
        ?string $paginate = null,
        ?int $limit = null,
    ): array {
        return [
            'data' => [
                ['id' => 'm_1', 'from' => ['id' => 'cust_1'], 'message' => 'Hi', 'created_time' => '2026-01-01T00:00:00+0000'],
            ],
        ];
    }

    public function getPageAccessToken(string $pageId, string $userAccessToken): string
    {
        return 'fake-page-access-token';
    }

    public function getMessageDetails(string $messageId, string $pageAccessToken): array
    {
        return [
            'id' => $messageId,
            'from' => ['id' => 'cust_1'],
            'to' => ['data' => [['id' => 'page_1']]],
            'message' => 'detail',
            'created_time' => '2026-01-01T00:00:00+0000',
        ];
    }

    public function sendMessage(string $customerId, array $options, string $pageAccessToken, string $pageId): array
    {
        return ['message_id' => 'sent_1', 'recipient_id' => $customerId];
    }

    public function sendMessageWithAttachment(
        string $customerId,
        array $options,
        string $pageAccessToken,
        string $pageId,
        int $userId,
    ): array {
        return ['message_id' => 'sent_attach_1', 'recipient_id' => $customerId];
    }
}
