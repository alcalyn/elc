<?php

namespace EL\CoreBundle\EventListener;

use JDare\ClankBundle\Event\ServerEvent;
use JDare\ClankBundle\Event\ClientEvent;
use JDare\ClankBundle\Event\ClientErrorEvent;
use EL\CoreBundle\Services\SessionService;

class ClankEventListener
{
    /**
     * @var SessionService
     */
    private $session;
    
    /**
     * @param \EL\CoreBundle\Services\SessionService $session
     */
    public function __construct(SessionService $session)
    {
        $this->session = $session;
    }
    
    public function onServerLaunched(ServerEvent $event)
    {
        echo 'Player session initialized'.PHP_EOL;
    }
    
    /**
     * Called whenever a client connects
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();
        $conn->Session->start();

        echo $conn->resourceId . " connected" . PHP_EOL;
        
        $event->stopPropagation();
    }

    /**
     * Called whenever a client disconnects
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        echo $conn->resourceId . " disconnected" . PHP_EOL;
        
        $event->stopPropagation();
    }

    /**
     * Called whenever a client errors
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e = $event->getException();

        echo "connection error occurred: " . $e->getMessage() . PHP_EOL;
        
        $event->stopPropagation();
    }
}
