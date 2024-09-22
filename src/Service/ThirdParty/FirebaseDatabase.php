<?php

namespace App\Service\ThirdParty;

use DateTime;
use Kreait\Firebase\Contract\Database;

class FirebaseDatabase
{

    private const PAGE_SIZE = 12;

    public function __construct(private Database $database)
    {}

    public function insertConversations(array $conversations): void
    {
        array_map(function($conversation) {
            $this->database->getReference('conversations/'.$conversation['conversationId'])->set($conversation);
        }, $conversations);
    }

    public function insertMessages(array $messages): void
    {
        array_map(function($message) {
            $this->database->getReference('messages/'.$message['messageId'])->set($message);
        }, $messages);
    }

    public function retrieveConversationsByPageId(string $pageId): array
    {
        $conversationsReference = $this->database
            ->getReference('conversations')
            ->orderByChild('pageId')
            ->equalTo($pageId)
            ->orderByValue('lastMessageTimestamp')
            ->getSnapshot()
            ->getValue();
        if (empty($conversationsReference)) {
            throw new \Exception('Conversations not found from firebase');
        }
        // Remove index value from firebase
        $conversations = array_values($conversationsReference);
        // Sort by latest updated
        usort($conversations, function ($a, $b) {
            return new DateTime($b['lastMessageTime']) <=> new DateTime($a['lastMessageTime']);
        });
        
        return $conversations;
    }

    public function retrieveMessagesByConversationId(string $conversationId): array|null
    {
        $messagesReference = $this->database
            ->getReference('messages')
            ->orderByChild('conversationId')
            ->equalTo($conversationId)
            ->getSnapshot()
            ->getValue();

        if (!$messagesReference) {
            return null;
        }
        // Remove index value from firebase
        $messages = array_values($messagesReference);
        // Sort by latest updated
        usort($messages, function ($a, $b) {
            return new DateTime($a['createdTime']) <=> new DateTime($b['createdTime']);
        });
        
        return $messages;
    }
}