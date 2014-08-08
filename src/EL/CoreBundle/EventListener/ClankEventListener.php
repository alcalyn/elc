<?php

namespace EL\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use JDare\ClankBundle\Event\ServerEvent;
use JDare\ClankBundle\Event\ClientEvent;
use JDare\ClankBundle\Event\ClientErrorEvent;
use EL\CoreBundle\Model\ELSession;

class ClankEventListener
{
    /**
     * @var Session
     */
    private $session;
    
    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    /**
     * Called on clank server launched.
     * Force this service to load and initialize session at server loading.
     * 
     * @param \JDare\ClankBundle\Event\ServerEvent $event
     */
    public function onServerLaunched(ServerEvent $event)
    {
        $this->session->start();
        
        echo 'Session initialized.'.PHP_EOL;
    }
    
    /**
     * Called whenever a client connects,
     * Start player session
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();
        $conn->Session->start();
        $conn->elSession = new ELSession($conn->Session);

        echo $conn->elSession->getPlayerPseudo() . ' connected' . PHP_EOL;
        
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

        echo $conn->elSession->getPlayer()->getPseudo() . ' disconnected' . PHP_EOL;
        
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

        echo 'connection error occurred: ' . $e->getMessage() . PHP_EOL;
        
        $event->stopPropagation();
    }
}
