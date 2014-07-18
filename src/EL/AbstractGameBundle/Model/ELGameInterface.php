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
     * @return \stdClass
     */
    public function createParty();
    
    /**
     * Return a form type for party
     * at the creation of a personalized party.
     * 
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getPartyType();
    
    /**
     * Retreive extended party from core party
     * Load extended party class,
     * only extended party configuration and date
     * (no slots...)
     * 
     * @param CoreParty $coreParty
     * 
     * @return \JsonSerializable
     */
    public function loadParty(CoreParty $coreParty);
    
    /**
     * Get game layout path, such as 'AbstractGameBundle:Adapter:game-layout.html.twig'.
     * Used to extends htmlhead and htmlend to add assets
     * 
     * @return string
     */
    public function getGameLayout();
    
    /**
     * Return a form template for your options
     * 
     * @return string template path, such as 'AbstractGameBundle:Adapter:optionsForm.html.twig'
     */
    public function getCreationFormTemplate();
    
    /**
     * Return infomations about extended options of current party.
     * Your options are accessible in twig though variable 'extendedOptions'
     * 
     * @param CoreParty $coreParty
     * @param stdClass $extendedParty
     * 
     * @return string template path, such as 'AbstractGameBundle:Adapter:displayOptions.html.twig'
     */
    public function getDisplayOptionsTemplate(CoreParty $coreParty, $extendedParty);
    
    /**
     * Return a default slots configuration
     * and some parameters
     * 
     * @return array
     */
    public function getSlotsConfiguration($options);
    
    /**
     * Controller of active party screen
     * 
     * @param string $_locale
     * @param PartyService $partyService
     * @param \stdClass $extendedParty
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function activeAction($_locale, PartyService $partyService, $extendedParty);
    
    /**
     * Controller of ended party screen (scores)
     * 
     * @param string $_locale
     * @param PartyService $partyService
     * @param \stdClass $extendedParty
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function endedAction($_locale, PartyService $partyService, $extendedParty);
    
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
}
