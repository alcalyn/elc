<?php

namespace EL\ChatBundle\Topic;

use Ratchet\ConnectionInterface;
use JDare\ClankBundle\Topic\TopicInterface;
use EL\ChatBundle\Services\ChatService;

class ChatTopic implements TopicInterface
{
    /**
     * @var ChatService
     */
    private $chat;
    
    /**
     * @param ChatService $chat
     */
    public function __construct(ChatService $chat)
    {
        $this->chat = $chat;
    }
    
    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $conn
     * @param $topic
     */
    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $player = $conn->elSession->getPlayer();
        $locale = $conn->locale;
        $message = $this->chat->getJoinMessage($player, $locale);
        
        $topic->broadcast(array(
            'sender'    => $conn->resourceId,
            'topic'     => $topic->getId(),
            'message'   => array(
                'content' => $message,
            ),
        ));
        
        $this->chat->log($topic, $message);
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $conn
     * @param $topic
     */
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        $player = $conn->elSession->getPlayer();
        $locale = $conn->locale;
        $message = $this->chat->getLeaveMessage($player, $locale);
        
        $topic->broadcast(array(
            'sender'    => $conn->resourceId,
            'topic'     => $topic->getId(),
            'message'   => array(
                'content' => $message,
            ),
        ));
        
        $this->chat->log($topic, $message);
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $player = $conn->elSession->getPlayer();
        
        $topic->broadcast(array(
            'sender'    => $conn->resourceId,
            'topic'     => $topic->getId(),
            'message'   => array(
                'content'       => $this->chat->parseMessage($event),
                'pseudo'        => htmlspecialchars($player->getPseudo()),
                'pseudoLink'    => $this->chat->getPlayerLink($player),
            ),
        ));
        
        $this->chat->log($topic, $event, $player);
    }
}
