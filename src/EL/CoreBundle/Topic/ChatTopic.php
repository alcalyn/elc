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
     * @return void
     */
    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast($conn->resourceId . ' has joined ' . $topic->getId());
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast($conn->resourceId . ' has left ' . $topic->getId());
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $player = $conn->elSession->getPlayer();
        
        $event['pseudo'] = $player->getPseudo();
        
        $topic->broadcast(array(
            'sender'    => $conn->resourceId,
            'topic'     => $topic->getId(),
            'message'   => $event,
        ));
    }
}
