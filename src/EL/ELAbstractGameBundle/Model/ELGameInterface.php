<?php

namespace EL\ELAbstractGameBundle\Model;

use EL\ELCoreBundle\Entity\Party as CoreParty;
use EL\ELCoreBundle\Services\PartyService;

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
     * Load extended party class,
     * only extended party configuration and date
     * (no slots...)
     * 
     * @return stdClass
     */
    public function loadParty($slug_party);
    
    /**
     * Can define customs rules to start party.
     * Return an ELUserException if cannot start, true otherwise
     * 
     * @return mixed true or ELUserException
     */
    public function canStart(PartyService $party_service);
    
    /**
     * Controller of active party screen
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function activeAction($_locale, PartyService $party_service);
    
    /**
     * Controller of ended party screen (scores)
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function endedAction($_locale, PartyService $party_service);
    
    /**
     * Return short message which will be displayed
     * under current party item in current parties widget
     * 
     * examples :
     *      Chess           Tic Tac Toe     Checkers        Poker
     *      "preparing..."  "starting..."   "with Homer"    "table noob, 2$/4$"
     * 
     * @return string
     */
    public function getCurrentDescription($_locale, PartyService $party_service);
    
    /**
     * Return boolean if it is my turn to play
     * 
     * @return boolean
     */
    public function isMyTurn(PartyService $party_service);
    
    /**
     * Return a clone of extended party.
     * Used when someone remake a party.
     * 
     * @param $_locale
     * @param $slug_party to clone
     * @param $clone_core_party just cloned
     * @return void nothing interesting
     */
    public function createClone($slug_party, CoreParty $clone_core_party);
}
