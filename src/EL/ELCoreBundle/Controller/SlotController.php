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
        $session            = $this->get('session');
        
        $party_service->setPartyBySlug($slug_party, $_locale);
        
        $result = $party_service->canJoin($player, true);
        
        switch ($result) {
            case PartyService::OK:
                $session->getFlashBag()->add(
                        'info',
                        'You have joined the party'
                );
                break;
            
            case PartyService::ENDED_PARTY:
                $session->getFlashBag()->add(
                        'danger',
                        'Error, this party has ended'
                );
                break;
            
            case PartyService::NO_FREE_SLOT:
                $session->getFlashBag()->add(
                        'danger',
                        'You cannot join the party, there is no free slot'
                );
                break;
            
            case PartyService::ALREADY_JOIN:
                $session->getFlashBag()->add(
                        'warning',
                        'You have already join this party'
                );
                break;
            
            default:
                $session->getFlashBag()->add(
                        'error',
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
    
    
    public function testAction()
    {
        // do something
        
        return $this->get('phax')->response(array(
            'test'  => 'ok',
        ));
    }
    
}
