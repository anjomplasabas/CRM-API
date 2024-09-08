<?php

namespace App\Service\ThirdParty;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    private const string BASE_URL = 'https://graph.facebook.com/v20.0/';

    private const array CONVERSATION_FIELDS = [
        'fields' => 'participants,name,messages.limit(1){from,message,created_time,attachments},message_count'
    ];

    private const array MESSAGE_DETAILS = [
        'fields' => 'from,to,message,created_time'
    ];

    private const array MESSAGE_FIELDS = [
        'fields' => 'from,to,message,created_time',
        'limit' => 12
    ];

    private const string MESSAGE_TAG = 'ACCOUNT_UPDATE';

    private const string MESSAGING_TYPE = 'MESSAGE_TAG';

    private const array MESSAGING_WEBVIEW_TEMPLATE = [
        'type' => 'template',
        'payload' => [
            'template_type' => 'button',
            'text' => 'Click the button to complete your order!',
            'buttons' => [
                [
                    'title' => 'ORDER HERE!',
                    'url' => 'https://localhost:3000/order-form?token=[TOKEN]',
                    'type' => 'web_url'
                ]
            ]
        ]
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger
    ) {}

    public function getConversations(string $pageId, string $pageAccessToken): array
    {
        $queryParameters = array_merge(
            self::CONVERSATION_FIELDS,
            [
                'access_token' => $pageAccessToken
            ]
        );
        $result = $this->client->request(
            'GET',
            self::BASE_URL . $pageId . '/conversations?' . http_build_query($queryParameters)
        );

        return json_decode($result->getContent(), true);
    }

    public function getMessages(string $conversationId, string $pageAccessToken, ?string $paginate): array
    {
        $queryParameters = array_merge(
            self::MESSAGE_FIELDS,
            [
                'access_token' => $pageAccessToken
            ]
        );
        if ($paginate) {
            $queryParameters = array_merge(
                $queryParameters,
                [
                    'after' => $paginate
                ]
            );
        }
        $result = $this->client->request(
            'GET',
            self::BASE_URL . $conversationId . '/messages?' . http_build_query($queryParameters)
        );

        return json_decode($result->getContent(), true);
    }

    public function getPageAccessToken(string $pageId, string $userAccessToken): string
    {
        $result = $this->client->request(
            'GET',
            self::BASE_URL . 'me/accounts?' . http_build_query([
                'access_token' => $userAccessToken
            ])
        );
        $pages = json_decode($result->getContent(), true);
        $page = array_filter($pages['data'], function ($page) use ($pageId) {
            return $page['id'] == $pageId;
        });
        $page = current($page);

        return $page['access_token'];
    }

    public function getMessageDetails(string $messageId, string $pageAccessToken): array
    {
        $queryParameters = array_merge(
            self::MESSAGE_DETAILS,
            [
                'access_token' => $pageAccessToken
            ]
        );
        $result = $this->client->request(
            'GET',
            self::BASE_URL . $messageId . '?' . http_build_query($queryParameters)
        );

        return json_decode($result->getContent(), true);
    }

    public function sendMessage(string $customerId, array $options, string $pageAccessToken, string $pageId): array
    {
        $mesage = [];
        if (isset($options['message'])) {
            $message =[
                'text' => $options['message']
            ];
        }
        $result = $this->client->request(
            'POST',
            self::BASE_URL . $pageId . '/messages?' . http_build_query([
                'access_token' => $pageAccessToken
            ]),
            [
                'json' => [
                    'recipient' => [
                        'id' => $customerId
                    ],
                    'messaging_type' => self::MESSAGING_TYPE,
                    'tag' => self::MESSAGE_TAG,
                    'message' => [
                        'text' => $options['message']
                    ]
                ]
            ]
        );

        return json_decode($result->getContent(), true);
    }
}
