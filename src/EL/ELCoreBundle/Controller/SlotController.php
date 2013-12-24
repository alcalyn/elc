<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Services\PartyService;

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
    
    
    public function refreshAction($params)
    {
        $slug_party = $params['slug_party'];
        $_locale    = $params['phax_metadata']['_locale'];
        
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
    
    
    public function openAction($params)
    {
        $slug_party = $params['slug_party'];
        $_locale    = $params['phax_metadata']['_locale'];
        $slot_index	= $params['slot_index'];
        $slot_open	= $params['slot_open'] === 'true';
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	        	->openSlot($slot_index, $slot_open)
        ;
        
        return $this->refreshAction($params);
    }
    
    
    public function ajaxJoinAction($params)
    {
        $slug_party = $params['slug_party'];
        $_locale    = $params['phax_metadata']['_locale'];
        $slot_index	= isset($params['slot_index']) ? intval($params['slot_index']) : -1 ;
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	    ;
        
        $result = $party_service->canJoin($this->getUser(), $slot_index, true);
        
        $message = $party_service->explainJoinResult($result);
        
        return $this->refreshAction($params);
    }
    
    
    public function banAction($params)
    {
    	$slug_party = $params['slug_party'];
        $_locale    = $params['phax_metadata']['_locale'];
        $player_id	= $params['player_id'];
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	    ;
	    
	    $ok = $party_service->ban($player_id);
	    
	    return $this->refreshAction($params);
    }
    
    
    public function reorderAction($params)
    {
    	$slug_party = $params['slug_party'];
        $_locale    = $params['phax_metadata']['_locale'];
        $indexes	= $params['new_order'];
        
        $party_service = $this
	        	->get('el_core.party')
	        	->setPartyBySlug($slug_party, $_locale)
	    ;
	    
	    $party_service->reorderSlots($indexes);
	    
    	return $this->refreshAction($params);
    }
    
}
