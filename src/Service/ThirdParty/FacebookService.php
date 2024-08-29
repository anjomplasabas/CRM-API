<?php

namespace App\Service\ThirdParty;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    private const string BASE_URL = 'https://graph.facebook.com/v20.0/';

    private const array CONVERSATION_FIELDS = [
       'fields' => 'participants,name,messages.limit(1){from,message,created_time},message_count'
    ];

    private const array MESSAGE_FIELDS = [
        'fields' => 'from,to,message,created_time',
        'limit' => 12
    ];

    public function __construct(
        private readonly HttpClientInterface $client
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
            self::BASE_URL.$pageId.'/conversations?'.http_build_query($queryParameters)
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
            self::BASE_URL.$conversationId.'/messages?'.http_build_query($queryParameters)
        );
        
        return json_decode($result->getContent(), true);
    }

    public function getPageAccessToken(string $pageId, string $userAccessToken): string
    {
        $result = $this->client->request(
            'GET',
            self::BASE_URL.'me/accounts?'.http_build_query([
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
}