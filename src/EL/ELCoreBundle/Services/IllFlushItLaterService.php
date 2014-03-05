<?php

namespace EL\ELCoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\ELCoreBundle\Services\IllDoItLaterService;

/**
 * Use IllDoItLater Service to persist and flush entities
 * at the end of stream
 * 
 * @author alcalyn
 */
class IllFlushItLaterService
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @var IllDoItLaterService 
     */
    private $illdoitlater;
    
    private $persistEntities;
    private $mergeEntities;
    
    
    public function __construct(EntityManager $em, IllDoItLaterService $illdoitlater)
    {
        $this->em           = $em;
        $this->illdoitlater = $illdoitlater;
        
        $this->clear();
        
        if ($this->illdoitlater->isEnabled()) {
            $this->addFlushCall();
        }
    }
    
    
    public function persist($entity)
    {
        if ($this->illdoitlater->isEnabled()) {
            $this->persistEntities []= $entity;
        } else {
            $this->em->persist($entity);
        }
        
        return $this;
    }
    
    
    public function merge($entity)
    {
        if ($this->illdoitlater->isEnabled()) {
            $this->mergeEntities []= $entity;
        } else {
            $this->em->merge($entity);
        }
        
        return $this;
    }
    
    public function flush()
    {
        if (!$this->illdoitlater->isEnabled()) {
            $this->em->flush();
        }
        
        return $this;
    }
    
    
    public function clear()
    {
        $this->persistEntities = array();
        $this->mergeEntities   = array();
        return $this;
    }
    
    
    private function addFlushCall()
    {
        $self = $this;
        
        $this->illdoitlater->addCall(function () use ($self) {
            $self->flushNow();
        }, 'illflushitlater-flush');
        
        return $this;
    }
    
    
    public function flushNow()
    {
        if ($this->illdoitlater->isEnabled() && $this->getEntitiesCount() > 0) {
            foreach ($this->persistEntities as $entity) {
                $this->em->persist($entity);
            }

            foreach ($this->mergeEntities as $entity) {
                $this->em->merge($entity);
            }
            
            $this->em->flush();
            $this->clear();
        }
        
        return $this;
    }
    
    
    
    public function getEntitiesCount()
    {
        return
            count($this->persistEntities) +
            count($this->mergeEntities);
    }
}
