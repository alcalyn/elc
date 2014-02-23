<?php

namespace EL\ELCoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELCoreBundle\Model\ELCoreException;
use EL\ELCoreBundle\Model\ELUserException;
use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Entity\Game;
use EL\ELCoreBundle\Services\PartyService;
use EL\ELCoreBundle\Form\Type\PartyType;
use EL\ELCoreBundle\Form\Entity\Options;
use EL\ELCoreBundle\Form\Type\OptionsType;

class PartyController extends Controller
{
    /**
     * Party creation page.
     * Contains form of base party (public/private...),
     * and extended game party
     * 
     * @Route(
     *      "/games/{slug}/creation",
     *      name = "elcore_party_creation"
     * )
     */
    public function createAction($_locale, $slug)
    {
        $em                 = $this->getDoctrine()->getManager();
        $request            = $this->getRequest();
        $partyService       = $this->get('el_core.party')->setGameBySlug($slug, $_locale, $this->container);
        $extendedGame       = $partyService->getExtendedGame();
        $coreParty          = $partyService->createParty();
        $extendedParty      = $extendedGame->getOptions();
        $extendedPartyType  = $extendedGame->getOptionsType();
        $options            = new Options($coreParty, $extendedParty);
        $optionsForm        = $this->createForm(new OptionsType($extendedPartyType), $options);
        
        $optionsForm->handleRequest($request);
        
        if ($optionsForm->isSubmitted()) {
            if ($optionsForm->isValid()) {
                $partyService->setParty($coreParty);
                
                $coreParty->setDateCreate(new \DateTime());
                
                $em->persist($coreParty);
                
                // notify extended game that party has been created with $extendedParty options
                $extendedGame->saveOptions($coreParty, $extendedParty);
                
                // get slots configuration from extended party depending of options
                $slotsConfiguration = $extendedGame->getSlotsConfiguration($extendedParty);
                
                // create slots from given slots configuration
                $partyService->createSlots($slotsConfiguration);
                
                // redirect to preparation page
                return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                    '_locale'       => $_locale,
                    'slug_game'     => $slug,
                    'slug_party'    => $coreParty->getSlug(),
                )));
            }
        }
        
        return $this->render('ELCoreBundle:Party:creation.html.twig', array(
            'game'          => $partyService->getGame(),
            'optionsForm'   => $optionsForm->createView(),
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
            return $this->redirectParty($_locale, $slug_game, $slug_party);
        }
        
        $player     = $this->get('el_core.session')->getPlayer();
        $canJoin    = true === $party_service->canJoin();
        $is_host    = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        $in_party   = $party_service->inParty();
        
        $this->get('el_core.js_vars')
                ->initPhaxController('slot')
                ->addContext('core_party', $party->jsonSerialize())
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
            'player'        => $player,
            'core_party'    => $party,
            'game'          => $party->getGame(),
            'slots'         => $party->getSlots(),
            'in_party'      => $in_party,
            'can_join'      => $canJoin,
            'is_host'       => $is_host,
        ));
    }
    
    /**
     * Controller used for url invitation
     * 
     * @Route(
     *      "/games/{slug_game}/{slug_party}/join",
     *      name = "elcore_party_join"
     * )
     */
    public function joinAction($_locale, $slug_game, $slug_party)
    {
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
        ;
        
        try {
            $party_service->join();
        } catch (ELUserException $e) {
            $e->addFlashMessage($this->get('session'));
        }
        
        return $this->redirectParty($_locale, $slug_game, $slug_party, $party_service->getParty());
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
                ->setPartyBySlug($slug_party, $_locale, $this->container)
        ;
        
        $player     = $this->get('el_core.session')->getPlayer();
        $party      = $party_service->getParty();
        $is_host    = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        $t          = $this->get('translator');
        $session    = $this->get('session');
        $flashbag   = $session->getFlashBag();
        $request    = $this->get('request');
        $action     = $request->request->get('action');
        
        switch ($action) {
            case 'run':
                if (!in_array($party->getState(), array(Party::PREPARATION, Party::STARTING))) {
                    break;
                }
                
                if ($is_host) {
                    try {
                        $party_service->start();
                    } catch (ELUserException $e) {
                        $e->addFlashMessage($session);
                    }
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
                
                try {
                    $party_service->join();
                } catch (ELUserException $e) {
                    $e->addFlashMessage($this->get('session'));
                }
                break;
            
            case 'remake':
                if ($party->getState() !== Party::ENDED) {
                    break;
                }
                
                $extended_game = $this
                        ->get($party_service->getGameServiceName())
                ;
                
                $remake_party = $this
                        ->get('el_core.party')
                        ->setPartyBySlug($slug_party, $_locale)
                        ->remake($extended_game)
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
        
        return $this->redirectParty($_locale, $slug_game, $slug_party, $party);
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
        
        if ($party->getState() !== Party::ACTIVE) {
            return $this->redirectParty($_locale, $slug_game, $slug_party, $party);
        }
        
        $extended_game  = $this->get($party_service->getGameServiceName());
        $jsVars         = $this->get('el_core.js_vars');
        
        $jsVars
            ->initPhaxController('party')
            ->addContext('core_party', $party->jsonSerialize())
            ->addContext('extended_party', $extended_game->loadParty($slug_party)->jsonSerialize())
        ;
        
        return $extended_game->activeAction($_locale, $party_service);
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
                ->setPartyBySlug($slug_party, $_locale)
        ;
        
        $party = $party_service->getParty();
        
        if ($party->getState() !== Party::ENDED) {
            return $this->redirectParty($_locale, $slug_game, $slug_party, $party);
        }
        
        $extended_game = $this->get($party_service->getGameServiceName());
        
        return $extended_game->endedAction($_locale, $party_service);
    }
    
    
    private function redirectParty($_locale, $slug_game, $slug_party, Party $party = null)
    {
        $parameters = array(
            '_locale'       => $_locale,
            'slug_game'     => $slug_game,
            'slug_party'    => $slug_party,
        );
        
        if (is_null($party)) {
            $party = $this
                    ->get('el_core.party')
                    ->setPartyBySlug($slug_party, $_locale)
                    ->getParty()
            ;
        }
        
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
