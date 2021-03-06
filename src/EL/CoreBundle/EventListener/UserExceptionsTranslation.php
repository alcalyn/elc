<?php

namespace EL\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use EL\CoreBundle\Exception\ELUserException;

class UserExceptionsTranslation
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    
    /**
     * @var Translator
     */
    private $translator;
    
    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Session $session, Translator $translator)
    {
        $this->flashBag = $session->getFlashBag();
        $this->translator = $translator;
    }
    
    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        
        if ($exception instanceof ELUserException) {
            $this->displayErrorMessage($exception);
            
            // Redirect to the current page
            $referer = $event->getRequest()->headers->get('referer');
            $event->setResponse(new RedirectResponse($referer));
            
            $event->stopPropagation();
        }
    }
    
    /**
     * Add a flashbag displaying translated error message from $exception
     * 
     * @param \EL\CoreBundle\Exception\ELUserException $exception
     */
    public function displayErrorMessage(ELUserException $exception)
    {
        $translatedMessage = $this->translator->trans($exception->getMessage(), array(), 'exceptions');
        $this->flashBag->add($exception->getType(), $translatedMessage);
    }
}
