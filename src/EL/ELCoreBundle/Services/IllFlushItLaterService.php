<?php

namespace EL\ELCoreBundle\Services;


/**
 * Use IllDoItLater Service to persist and flush entities
 * at the end of stream
 * 
 * @author alcalyn
 */
class IllFlushItLaterService
{
    private $em;
    
    /**
     * @var IllDoItLaterService 
     */
    private $illdoitlater;
    
    private $persist_entities;
    private $merge_entities;
    
    
    public function __construct($em, $illdoitlater)
    {
        $this->em = $em;
        $this->illdoitlater = $illdoitlater;
        
        $this->clear();
        
        if ($this->illdoitlater->isEnabled()) {
            $this->addFlushCall();
        }
    }
    
    
    public function persist($entity)
    {
        if ($this->illdoitlater->isEnabled()) {
            $this->persist_entities []= $entity;
        } else {
            $this->em->persist($entity);
        }
        
        return $this;
    }
    
    
    public function merge($entity)
    {
        if ($this->illdoitlater->isEnabled()) {
            $this->merge_entities []= $entity;
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
        $this->persist_entities = array();
        $this->merge_entities   = array();
        return $this;
    }
    
    
    private function addFlushCall()
    {
        $self = $this;
        
        $this->illdoitlater->addCall(function () use($self) {
            $self->flushNow();
        }, 'illflushitlater-flush');
        
        return $this;
    }
    
    
    public function flushNow()
    {
        if ($this->illdoitlater->isEnabled() && $this->getEntitiesCount() > 0) {
            foreach ($this->persist_entities as $entity) {
                $this->em->persist($entity);
            }

            foreach ($this->merge_entities as $entity) {
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
            count($this->persist_entities) +
            count($this->merge_entities);
    }
    
    
}