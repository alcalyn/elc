<?php

namespace EL\CoreBundle\Topic;

use Ratchet\ConnectionInterface;
use JDare\ClankBundle\Topic\TopicInterface;
use EL\CoreBundle\Services\ChatService;

class ChatTopic implements TopicInterface
{
    /**
     * @var \EL\CoreBundle\Services\ChatService
     */
    private $chat;
    
    /**
     * @param \EL\CoreBundle\Services\ChatService $chat
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
        
        $topic->broadcast(array(
            'sender'    => $conn->resourceId,
            'topic'     => $topic->getId(),
            'message'   => array(
                'content' => $player->getPseudo().' a rejoint le chat.',
            ),
        ));
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
        
        $topic->broadcast(array(
            'sender'    => $conn->resourceId,
            'topic'     => $topic->getId(),
            'message'   => array(
                'content' => $player->getPseudo().' a quitté le chat.',
            ),
        ));
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
                'content'   => $event,
                'pseudo'    => $player->getPseudo(),
            ),
        ));
    }
}
