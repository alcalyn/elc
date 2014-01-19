<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Form\Entity\PartyOptions;
use EL\ElCoreBundle\Form\Type\PartyOptionsType;
use EL\ELCoreBundle\Services\PartyService;
use EL\ELCoreBundle\Model\ELCoreException;
use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Entity\Game;


class PartyController extends Controller
{
    
    /**
     * @Route(
     *      "/games/{slug}/creation",
     *      name = "elcore_party_creation"
     * )
     */
    public function createAction($_locale, $slug)
    {
        $party_service = $this
                ->get('el_core.party')
                ->setGameBySlug($slug, $_locale);
        
        $extended_party_service = $this
                ->get($party_service->getGameServiceName());
        
        $party_options = new PartyOptions();
        $party_options
                ->setTitle($party_service->generateRandomTitle())
                ->setSpecialPartyOptions($extended_party_service->getOptions());
        
        $party_options_type = new PartyOptionsType($extended_party_service->getOptionsType());
        
        $party_options_form = $this->createForm($party_options_type, $party_options);
        $party_options_form->handleRequest($this->getRequest());
        
        if ($party_options_form->isValid()) {
            $party = $party_service
                    ->createParty($party_options);
            $party_service
                    ->setParty($party);
            
            $special_party_options = $party_options
                    ->getSpecialPartyOptions();
            
            $em = $this
                    ->getDoctrine()
                    ->getManager();
            
            if ($extended_party_service->saveOptions($party, $special_party_options)) {
                $party_service
                        ->createSlots($extended_party_service->getSlotsConfiguration($special_party_options));
                
                return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                    '_locale'       => $_locale,
                    'slug_game'     => $slug,
                    'slug_party'    => $party->getSlug(),
                )));
            }
        }
        
        return $this->render('ELCoreBundle:Party:creation.html.twig', array(
            'game'             => $party_service->getGame(),
            'party_options'    => $party_options_form->createView(),
        ));
    }
    
    
    /**
     * @Route(
     *      "/games/{slug_game}/{slug_party}/preparation",
     *      name = "elcore_party_preparation"
     * )
     */
    public function prepareAction($_locale, $slug_game, $slug_party)
    {
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale);
        
        $party = $party_service->getParty();
        
        if (!in_array($party->getState(), array(Party::PREPARATION, Party::STARTING))) {
        	return $this->_redirect($_locale, $slug_game, $slug_party);
        }
        
        $player = $this->getUser();
        
        $canJoin = $party_service
                ->canJoin($player);
        
        $is_host = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        $in_party = $canJoin === PartyService::ALREADY_JOIN;
        
        $this->get('el_core.js_vars')
        		->initPhaxController('slot')
        		->addContext('is_host', $is_host)
        		->addContext('slug_party', $slug_party)
        		->addContext('in_party', $in_party)
        		->useTrans('open')
        		->useTrans('close')
        		->useTrans('slot.open')
        		->useTrans('slot.closed')
        		->useTrans('delete.slot')
        		->useTrans('change.slot')
        		->useTrans('ban')
        		->useTrans('create.account')
        		->useTrans('invite.player')
        		->useTrans('invite.cpu')
        ;
        
        return $this->render('ELCoreBundle:Party:preparation.html.twig', array(
        	'player'		=> $player,
            'party'         => $party,
            'game'          => $party->getGame(),
            'slots'         => $party->getSlots(),
            'in_party'      => $in_party,
            'can_join'      => $canJoin === PartyService::OK,
            'is_host'		=> $is_host,
        ));
    }
    
    
	/**
     * @Route(
     *      "/games/{slug_game}/{slug_party}/preparation/action",
     *      name = "elcore_party_preparation_action"
     * )
     */
    public function prepareActionAction($_locale, $slug_game, $slug_party)
    {
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
        ;
        
        $player		= $this->getUser();
        $party		= $party_service->getParty();
        $is_host	= is_object($player) && is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        $t			= $this->get('translator');
        $flashbag	= $this->get('session')->getFlashBag();
        $request	= $this->get('request');
        $action		= $request->request->get('action');
        $error		= null;
        
        switch ($action) {
        	case 'run':
		        if (!in_array($party->getState(), array(Party::PREPARATION, Party::STARTING))) {
		            break;
		        }
		        
        		if ($is_host) {
        			$status = $party_service->start();
        			$flashbag->add('success', $t->trans('party.has.started'));
        		} else {
        			$flashbag->add('danger', $t->trans('cannot.start.youarenothost'));
        		}
        	break;
        	
        	case 'cancel':
        		
        	break;
        	
        	case 'leave':
        	
        	break;
        	
        	case 'join':
        		if ($party->getState() !== Party::PREPARATION) {
		            break;
		        }
		        
        		$status = $party_service->join($player);
        		$result = $party_service->explainJoinResult($status);
        		$flashbag->add($result['type'], $result['message']);
        	break;
        	
        	case 'remake':
		        if ($party->getState() !== Party::ENDED) {
		            break;
		        }
		        
        		$extended_party_service = $this
        				->get($party_service->getGameServiceName())
        		;
        		
        		$remake_party = $this
		                ->get('el_core.party')
		                ->setPartyBySlug($slug_party, $_locale)
		                ->remake($extended_party_service)
		        ;
		        
		        return $this->redirect($this->generateUrl('elcore_party_preparation', array(
	                '_locale'       => $_locale,
	                'slug_game'     => $remake_party->getGame()->getSlug(),
	                'slug_party'    => $remake_party->getSlug(),
	            )));
        	break;
        	
        	default:
        		throw new ELCoreException('Unknown action : "'.$action.'"');
        }
        
        return $this->_redirect($_locale, $slug_game, $slug_party);
    }
    
    
    /**
     * Redirect on /preparation or /XXX if not active
     * 
     * @Route(
     *      "/games/{slug_game}/{slug_party}",
     *      name = "elcore_party"
     * )
     */
    public function activeAction($_locale, $slug_game, $slug_party)
    {
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
        ;
        
        $party = $party_service->getParty();
        
        if (in_array($party->getState(), array(Party::PREPARATION, Party::STARTING))) {
            return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                '_locale'       => $_locale,
                'slug_game'     => $slug_game,
                'slug_party'    => $slug_party,
            )));
        }
        
        if ($party->getState() === Party::ENDED) {
            return $this->redirect($this->generateUrl('elcore_party_ended', array(
                '_locale'       => $_locale,
                'slug_game'     => $slug_game,
                'slug_party'    => $slug_party,
            )));
        }
        
        $extended_party_service = $this->get($party_service->getGameServiceName());
        $party_extended = $extended_party_service->loadParty($_locale, $slug_party);
        $jsVars = $this->get('el_core.js_vars');
        
        $jsVars
        	->addContext('core_party', $party->jsonSerialize())
        	->addContext('extended_party', $extended_party_service->loadParty($_locale, $slug_party)->jsonSerialize())
        ;
        
        return $extended_party_service->activeAction($_locale, $party_service);
    }
    
    
    /**
     * @Route(
     *      "/games/{slug_game}/{slug_party}/results",
     *      name = "elcore_party_ended"
     * )
     */
    public function endedAction($_locale, $slug_game, $slug_party)
    {
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale);
        
        $extended_party_service = $this->get($party_service->getGameServiceName());
        
        return $extended_party_service->endedAction($_locale, $party_service);
    }
    
    
    private function _redirect($_locale, $slug_game, $slug_party)
    {
    	$parameters = array(
            '_locale'       => $_locale,
            'slug_game'     => $slug_game,
            'slug_party'    => $slug_party,
        );
        
        $party = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
                ->getParty()
		;
        
        switch ($party->getState()) {
        	case Party::PREPARATION:
        	case Party::STARTING:
        		return $this->redirect($this->generateUrl('elcore_party_preparation', $parameters));
        	
        	case Party::ACTIVE:
        		return $this->redirect($this->generateUrl('elcore_party', $parameters));
        	
        	case Party::ENDED:
        		return $this->redirect($this->generateUrl('elcore_party_ended', $parameters));
        	
        	default:
        		throw new ELCoreException('Unknown party state : #'.$party->getState());
        }
    }
}
