<?php

namespace EL\AbstractGameBundle\Model;

use EL\CoreBundle\Entity\Party as CoreParty;
use EL\CoreBundle\Services\PartyService;

interface ELGameInterface
{
    /**
     * Return an object to hydrate by options form.
     * Can have default values
     * 
     * @return stdClass
     */
    public function getOptions();
    
    /**
     * Return a form type which extends base form
     * at the creation of a personalized party.
     * 
     * @return AbstractType
     */
    public function getOptionsType();
    
    /**
     * Called then optionsType form has been posted.
     * Use this callback to save party options in database
     * and return true, if options are valid
     * 
     * Return false to refuse options
     * 
     * @return boolean
     */
    public function saveOptions(CoreParty $coreParty, $options);
    
    /**
     * Retreive options from core party
     * 
     * @return boolean
     */
    public function loadOptions(CoreParty $coreParty);
    
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
    public function loadParty($slugParty);
    
    /**
     * Can define customs rules to start party.
     * Return an ELUserException if cannot start, true otherwise
     * 
     * @return mixed true or ELUserException
     */
    public function canStart(PartyService $partyService);
    
    /**
     * Controller of active party screen
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function activeAction($_locale, PartyService $partyService);
    
    /**
     * Controller of ended party screen (scores)
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function endedAction($_locale, PartyService $partyService);
    
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
    public function getCurrentDescription($_locale, PartyService $partyService);
    
    /**
     * Return boolean if it is my turn to play
     * 
     * @return boolean
     */
    public function isMyTurn(PartyService $partyService);
    
    /**
     * Return a clone of extended party.
     * Used when someone remake a party.
     * 
     * @param $slugParty to clone
     * @param $corePartyClone just cloned
     * @return void nothing interesting
     */
    public function createRemake($slugParty, CoreParty $corePartyClone);
}
