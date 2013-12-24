<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Form\Entity\PartyOptions;
use EL\ElCoreBundle\Form\Type\PartyOptionsType;
use EL\ELCoreBundle\Services\PartyService;
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
        
        $game_service = $this
                ->get($party_service->getGameServiceName());
        
        $party_options = new PartyOptions();
        $party_options
                ->setTitle($party_service->generateRandomTitle())
                ->setSpecialPartyOptions($game_service->getOptions());
        
        $party_options_type = new PartyOptionsType($game_service->getOptionsType());
        
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
            
            if ($game_service->saveOptions($party, $special_party_options, $em)) {
                $party_service
                        ->createSlots($game_service->getSlotsConfiguration($special_party_options));
                
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
        
        $player = $this->getUser();
        $party  = $party_service->getParty();
        
        $canJoin = $party_service
                ->canJoin($player);
        
        $is_host = $player->getId() === $party->getHost()->getId();
        $in_party = $canJoin === PartyService::ALREADY_JOIN;
        
        $this->get('el_core.js_vars')
        		->initPhaxController('slot')
        		->addContext('is_host', $is_host)
        		->addContext('slug_party', $slug_party)
        		->addContext('in_party', $in_party)
        		->useTrans('slot.open')
        		->useTrans('slot.closed')
        		->useTrans('create.account')
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
                ->setPartyBySlug($slug_party, $_locale);
        
        $party = $party_service->getParty();
        
        if ($party->getState() === Party::PREPARATION) {
            return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                '_locale'       => $_locale,
                'slug_game'     => $slug_game,
                'slug_party'    =>$slug_party,
            )));
        }
        
        if ($party->getState() === Party::ENDED) {
            return $this->redirect($this->generateUrl('elcore_party_ended', array(
                '_locale'       => $_locale,
                'slug_game'     => $slug_game,
                'slug_party'    =>$slug_party,
            )));
        }
        
        $game_service = $this
                ->get($party_service->getGameServiceName());
        
        return $this->render('ELCoreBundle:Party:creation.html.twig', array(
            'game'          => $party_service->getGame(),
            'party'         => $party,
        ));
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
        
        $party = $party_service->getParty();
        
        $game_service = $this
                ->get($party_service->getGameServiceName());
        
        return $this->render('ELCoreBundle:Party:creation.html.twig', array(
            'game'          => $party_service->getGame(),
            'party'         => $party,
        ));
    }
}
