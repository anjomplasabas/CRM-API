<?php

namespace App\Service\ThirdParty;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    private const BASE_URL = 'https://graph.facebook.com/v20.0/';
    private const CONVERSATION_FIELDS = [
        'fields' => 'participants,name,messages.limit(1){from,message,created_time,attachments},message_count',
    ];
    private const MESSAGE_DETAILS = [
        'fields' => 'from,to,message,attachments,created_time',
    ];
    private const MESSAGE_FIELDS = [
        'fields' => 'from,to,message,attachments,created_time',
        'limit' => 12,
    ];
    private const MESSAGE_TAG = 'ACCOUNT_UPDATE';
    private const MESSAGING_TYPE = 'MESSAGE_TAG';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly string $baseUrl
    ) {}

    public function getConversations(
        string $pageId,
        string $pageAccessToken
    ): array {
        $queryParameters = array_merge(self::CONVERSATION_FIELDS, [
            'access_token' => $pageAccessToken,
        ]);
        $result = $this->client->request(
            'GET',
            self::BASE_URL . $pageId . '/conversations?' . http_build_query($queryParameters)
        );

        return json_decode($result->getContent(), true);
    }

    public function getMessages(
        string $conversationId,
        string $pageAccessToken,
        ?string $paginate = null,
        ?int $limit = null
    ): array {
        $queryParameters = array_merge(self::MESSAGE_FIELDS, [
            'access_token' => $pageAccessToken,
        ]);
        if ($paginate) {
            $queryParameters = array_merge($queryParameters, [
                'after' => $paginate,
            ]);
        }
        if ($limit) {
            $queryParameters['limit'] = $limit;
        }
        $result = $this->client->request(
            'GET',
            self::BASE_URL . $conversationId . '/messages?' . http_build_query($queryParameters)
        );

        return json_decode($result->getContent(), true);
    }

    public function getPageAccessToken(
        string $pageId,
        string $userAccessToken
    ): string {
        $result = $this->client->request(
            'GET',
            self::BASE_URL . 'me/accounts?' . http_build_query([
                'access_token' => $userAccessToken,
            ])
        );
        $pages = json_decode($result->getContent(), true);
        $page = array_filter($pages['data'], function ($page) use ($pageId) {
            return $page['id'] == $pageId;
        });
        $page = current($page);

        return $page['access_token'];
    }

    public function getMessageDetails(
        string $messageId,
        string $pageAccessToken
    ): array {
        $queryParameters = array_merge(self::MESSAGE_DETAILS, [
            'access_token' => $pageAccessToken,
        ]);
        $result = $this->client->request(
            'GET',
            self::BASE_URL . $messageId . '?' . http_build_query($queryParameters)
        );

        return json_decode($result->getContent(), true);
    }

    public function sendMessage(
        string $customerId,
        array $options,
        string $pageAccessToken,
        string $pageId
    ): array {
        $result = $this->client->request(
            'POST',
            self::BASE_URL . $pageId . '/messages?' . http_build_query([
                'access_token' => $pageAccessToken,
            ]),
            [
                'json' => [
                    'recipient' => [
                        'id' => $customerId,
                    ],
                    'messaging_type' => self::MESSAGING_TYPE,
                    'tag' => self::MESSAGE_TAG,
                    'message' => [
                        'text' => $options['message'],
                    ],
                ],
            ]
        );

        return json_decode($result->getContent(), true);
    }

    public function sendMessageWithAttachment(
        string $customerId,
        array $options,
        string $pageAccessToken,
        string $pageId,
        int $userId,
    ): array {
        $options = array_merge($options, [
            'pageId' => $pageId,
            'facebookProfileId' => $customerId,
            'userId' => $userId
        ]);
        $token = base64_encode(json_encode($options));
        $tmp = [
            'recipient' => [
                'id' => $customerId,
            ],
            'tag' => self::MESSAGE_TAG,
            'message' => [
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'generic',
                        'text' => 'Click the button to complete your order!',
                        'buttons' => [
                            [
                                'type' => 'web_url',
                                'url' => $this->baseUrl.'?token=' . $token,
                                'title' => 'ORDER NOW!',
                                'webview_height_ratio' => 'tall',
                                'messenger_extensions' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->logger->info(' === PAYLOAD FOR WEB URL === ');
        $this->logger->info(json_encode($tmp));
        $result = $this->client->request(
            'POST',
            self::BASE_URL . $pageId . '/messages?' . http_build_query([
                'access_token' => $pageAccessToken,
            ]),
            [
                'json' => [
                    'recipient' => [
                        'id' => $customerId,
                    ],
                    'tag' => self::MESSAGE_TAG,
                    'message' => [
                        'attachment' => [
                            'type' => 'template',
                            'payload' => [
                                'template_type' => 'button',
                                'text' => 'Click the button to complete your order!',
                                'buttons' => [
                                    [
                                        'type' => 'web_url',
                                        'url' => $this->baseUrl.'?token=' . $token,
                                        'title' => 'ORDER NOW!',
                                        'webview_height_ratio' => 'tall',
                                        'messenger_extensions' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
        
        return json_decode($result->getContent(), true);
    }
}
