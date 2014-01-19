<?php

namespace EL\ELAbstractGameBundle\Model;

use EL\ELCoreBundle\Entity\Party as CoreParty;


interface ELGameInterface
{
    
    /**
     * Return a form type which extends base form
     * at the creation of a personalized party.
     * 
     * @return AbstractForm
     */
    public function getOptionsType();
    
    /**
     * Return an object to hydrate by options form.
     * Can have default values
     * 
     * @return stdClass
     */
    public function getOptions();
    
    /**
     * Called then optionsType form has been posted.
     * Use this callback to save party options in database
     * and return true, if options are valid
     * 
     * Return false to refuse options
     * 
     * @return boolean
     */
    public function saveOptions(CoreParty $core_party, $options);
    
    /**
     * Retreive options from core party
     * 
     * @return boolean
     */
    public function loadOptions(CoreParty $core_party);
    
    /**
     * Return a default slots configuration
     * and some parameters
     * 
     * @return array
     */
    public function getSlotsConfiguration($options);
    
    /**
     * Load extended party class
     * 
     * @return stdClass
     */
    public function loadParty($_locale, $slug_party);
    
    /**
     * Controller of active party screen
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function activeAction($_locale, $party_service);
    
    /**
     * Controller of ended party screen (scores)
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function endedAction($_locale, $party_service);
    
    /**
     * Return a clone of extended party.
     * Used when someone remake a party.
     * 
     * @param $_locale
     * @param $slug_party to clone
     * @param $clone_core_party just cloned
     * @return void nothing interesting
     */
    public function createClone($slug_party, $clone_core_party);
    
    
}
