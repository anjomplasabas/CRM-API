<?php

namespace App\Tests\Support;

use App\Service\ThirdParty\FirebaseDatabase;

class FakeFirebaseDatabase extends FirebaseDatabase
{
    private array $conversations = [];
    private array $messages = [];

    public function __construct()
    {
    }

    public function insertConversations(array $conversations): void
    {
        foreach ($conversations as $conversation) {
            $this->conversations[$conversation['conversationId']] = $conversation;
        }
    }

    public function insertMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->messages[$message['messageId']] = $message;
        }
    }

    public function retrieveConversationsByPageId(string $pageId): array
    {
        $matches = array_values(array_filter(
            $this->conversations,
            static fn (array $c) => ($c['pageId'] ?? null) === $pageId,
        ));

        if ($matches === []) {
            throw new \Exception('Conversations not found from firebase');
        }

        return $matches;
    }

    public function retrieveMessagesByConversationId(string $conversationId): array|null
    {
        $matches = array_values(array_filter(
            $this->messages,
            static fn (array $m) => ($m['conversationId'] ?? null) === $conversationId,
        ));

        return $matches === [] ? null : $matches;
    }
}
