<?php

namespace App\Service\ThirdParty;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    private const string BASE_URL = 'https://graph.facebook.com/v20.0/';

    private const array CONVERSATION_FIELDS = [
       'fields' => 'participants,name,messages.limit(1){from,message,created_time},message_count'
    ];

    public function __construct(
        private readonly HttpClientInterface $client
    ) {}

    public function getConversations(string $pageId, string $pageAccessToken)
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
}