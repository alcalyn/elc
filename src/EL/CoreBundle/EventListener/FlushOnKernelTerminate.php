<?php

namespace EL\CoreBundle\EventListener;

use Doctrine\ORM\EntityManager;

class FlushOnKernelTerminate
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Flush on kernel terminate.
     */
    public function onKernelTerminate()
    {
        $this->em->flush();
    }
}
