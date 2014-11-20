<?php

namespace EL\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\TranslatorInterface;
use EL\Bundle\CoreBundle\Exception\ELUserException;

class UserExceptionsTranslation
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Session $session, TranslatorInterface $translator)
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
     * @param \EL\Bundle\CoreBundle\Exception\ELUserException $exception
     */
    public function displayErrorMessage(ELUserException $exception)
    {
        $translatedMessage = $this->translator->trans($exception->getMessage(), array(), 'exceptions');
        $this->flashBag->add($exception->getType(), $translatedMessage);
    }
}
