<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use Phax\CoreBundle\Model\PhaxResponse;
use EL\CoreBundle\Exception\ELCoreException;

class PartyAjaxController extends Controller
{
    /**
     * Create a party
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function createAction(PhaxAction $phaxAction)
    {
        return $this->get('phax')->reaction(array(
        ));
    }
    
    /**
     * Return party instance from slug
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshAction(PhaxAction $phaxAction)
    {
        self::checkAction($phaxAction);
        
        $locale     = $phaxAction->get('locale');
        $slugGame   = $phaxAction->get('slugGame');
        $slugParty  = $phaxAction->get('slugParty');
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $locale)
        ;
        
        return $this->get('phax')->reaction(array(
            'coreParty' => $partyService->getParty()->jsonSerialize(),
        ));
    }
    
    /**
     * Return party instance from slug
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshPreparationAction(PhaxAction $phaxAction)
    {
        return $this->refreshAction($phaxAction);
    }
    
    /**
     * Return party instance from slug
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshStartingAction(PhaxAction $phaxAction)
    {
        return $this->refreshAction($phaxAction);
    }
    
    /**
     * Return party instance from slug
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshActiveAction(PhaxAction $phaxAction)
    {
        return $this->refreshAction($phaxAction);
    }
    
    /**
     * Return party instance from slug,
     * and players who are either gone or remake the party
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshEndedAction(PhaxAction $phaxAction)
    {
        self::checkAction($phaxAction);
        
        $locale     = $phaxAction->get('locale');
        $slugGame   = $phaxAction->get('slugGame');
        $slugParty  = $phaxAction->get('slugParty');
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $locale)
        ;
        
        $playersInRemake = $partyService->getPlayersInRemakeParty();
        
        return $this->get('phax')->reaction(array(
            'coreParty'         => $partyService->getParty()->jsonSerialize(),
            'playersInRemake'   => $playersInRemake,
        ));
    }
    
    /**
     * Check if needed request parameters are defined
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @throws ELCoreException
     */
    private static function checkAction(PhaxAction $phaxAction)
    {
        foreach (array('locale', 'slugGame', 'slugParty') as $parameter) {
            if (!$phaxAction->has($parameter)) {
                throw new ELCoreException(
                    "partyAjax::refreshAction : $parameter must be defined"
                );
            }
        }
    }
}
