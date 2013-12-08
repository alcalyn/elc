<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Services\PartyService;

class SlotController extends Controller
{
    /**
     * @Route(
     *      "/games/{slug_game}/{slug_party}/join",
     *      name = "elcore_party_join"
     * )
     */
    public function joinAction($_locale, $slug_game, $slug_party)
    {
        $session_service    = $this->get('el_core.session');
        $party_service      = $this->get('el_core.party');
        $player             = $session_service->getPlayer();
        $flashbag           = $this->get('session')->getFlashBag();
        
        $party_service->setPartyBySlug($slug_party, $_locale);
        
        $result = $party_service->canJoin($player, true);
        
        switch ($result) {
            case PartyService::OK:
                $flashbag->add(
                        'info',
                        'You have joined the party'
                );
                break;
            
            case PartyService::ENDED_PARTY:
                $flashbag->add(
                        'danger',
                        'Error, this party has ended'
                );
                break;
            
            case PartyService::NO_FREE_SLOT:
                $flashbag->add(
                        'danger',
                        'You cannot join the party, there is no free slot'
                );
                break;
            
            case PartyService::ALREADY_JOIN:
                $flashbag->add(
                        'warning',
                        'You have already join this party'
                );
                break;
            
            case PartyService::STARTED_PARTY:
                $flashbag->add(
                        'danger',
                        'This party has already started, and is not in room mode'
                );
                break;
            
            default:
                $flashbag->add(
                        'danger',
                        'You cannot join the party, unknown error : #'.$result
                );
                break;
            
        }
        
        return $this->redirect($this->generateUrl('elcore_party_preparation', array(
            '_locale'       => $_locale,
            'slug_game'     => $slug_game,
            'slug_party'    => $slug_party,
        )));
    }
    
    
    public function refreshAction($params)
    {
        $session_service    = $this->get('el_core.session');
        $party_service      = $this->get('el_core.party');
        $player             = $session_service->getPlayer();
        $session            = $this->get('session');
        
        $slug_party = $params['slug_party'];
        $_locale    = $params['phax_metadata']['_locale'];
        
        $party = $party_service
                ->setPartyBySlug($slug_party, $_locale)
                ->getParty()
        ;
        
        $slots = array();
        
        foreach ($party->getSlots() as $slot) {
            $slots []= $slot;
        }
        
        return $this->get('phax')->reaction(array(
            'party'     => $party,
            'slots'     => $slots,
        ));
    }
    
}
