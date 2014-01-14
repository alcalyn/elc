<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Services\PartyService;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxResponse;

class SlotController extends Controller
{
    /**
     * This controller is important
     * (not a duplicate of ajaxJoinAction())
     * because it is used for its url
     * (when sent to a friend or typed id browser)
     * 
     * @Route(
     *      "/games/{slug_game}/{slug_party}/join",
     *      name = "elcore_party_join"
     * )
     */
    public function joinAction($_locale, $slug_game, $slug_party)
    {
        $party_service      = $this->get('el_core.party');
        $player             = $this->getUser();
        $flashbag           = $this->get('session')->getFlashBag();
        
        $party_service->setPartyBySlug($slug_party, $_locale);
        $result = $party_service->canJoin($player, -1, true);
        $message = $party_service->explainJoinResult($result);
        $flashbag->add($message['type'], $message['message']);
        
        return $this->redirect($this->generateUrl('elcore_party_preparation', array(
            '_locale'       => $_locale,
            'slug_game'     => $slug_game,
            'slug_party'    => $slug_party,
        )));
    }
    
    
    public function refreshAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        
        $party = $this
        		->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
                ->getParty()
        ;
        
        $slots = array();
        
        foreach ($party->getSlots() as $slot) {
            $slots[$slot->getPosition() - 1] = $slot->jsonSerialize();
        }
        
        return $this->get('phax')->reaction(array(
            'party'     => $party->jsonSerialize(),
            'slots'     => $slots,
        ));
    }
    
    
    public function openAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $slot_index	= $phax_action->slot_index;
        $slot_open	= $phax_action->slot_open === 'true';
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	        	->openSlot($slot_index, $slot_open)
        ;
        
        return $this->refreshAction($phax_action);
    }
    
    
    public function ajaxJoinAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $slot_index	= isset($phax_action->slot_index) ? intval($phax_action->slot_index) : -1 ;
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	    ;
        
        $result = $party_service->canJoin($this->getUser(), $slot_index, true);
        
        $message = $party_service->explainJoinResult($result);
        
        return $this->refreshAction($phax_action);
    }
    
    
    public function banAction(PhaxAction $phax_action)
    {
    	$slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $player_id	= $phax_action->player_id;
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	    ;
	    
	    $ok = $party_service->ban($player_id);
	    
	    return $this->refreshAction($phax_action);
    }
    
    
    public function reorderAction(PhaxAction $phax_action)
    {
    	$slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $indexes	= $phax_action->new_order;
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	    ;
	    
	    $party_service->reorderSlots($indexes);
	    
    	return $this->refreshAction($phax_action);
    }
    
}
