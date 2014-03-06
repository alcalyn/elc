<?php

namespace EL\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\CoreBundle\Model\ELCoreException;
use EL\CoreBundle\Model\ELUserException;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Entity\Game;
use EL\CoreBundle\Services\PartyService;
use EL\CoreBundle\Form\Type\PartyType;
use EL\CoreBundle\Form\Entity\Options;
use EL\CoreBundle\Form\Type\OptionsType;
use EL\CoreBundle\Model\Slug;

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
        $em                     = $this->getDoctrine()->getManager();
        $request                = $this->getRequest();
        $partyService           = $this->get('el_core.party')->setGameBySlug($slug, $_locale, $this->container);
        $extendedGame           = $partyService->getExtendedGame();
        $coreParty              = $partyService->createParty();
        $extendedOptions        = $extendedGame->createParty();
        $extendedOptionsType    = $extendedGame->getPartyType();
        $options                = new Options($coreParty, $extendedOptions);
        $optionsForm            = $this->createForm(new OptionsType($extendedOptionsType), $options);
        
        $optionsForm->handleRequest($request);
        
        if ($optionsForm->isSubmitted()) {
            if ($optionsForm->isValid()) {
                $partyService->setParty($coreParty);
                
                $coreParty
                        ->setDateCreate(new \DateTime())
                        ->setSlug(Slug::slug($coreParty->getTitle()))
                ;
                
                $em->persist($coreParty);
                
                // notify extended game that party has been created with $extendedOptions options
                $extendedGame->saveParty($coreParty, $extendedOptions);
                
                // get slots configuration from extended party depending of options
                $slotsConfiguration = $extendedGame->getSlotsConfiguration($extendedOptions);
                
                // create slots from given slots configuration
                $partyService->createSlots($slotsConfiguration);
                
                // redirect to preparation page
                return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                    '_locale'       => $_locale,
                    'slugGame'     => $slug,
                    'slugParty'    => $coreParty->getSlug(),
                )));
            }
        }
        
        return $this->render('CoreBundle:Party:creation.html.twig', array(
            'game'                  => $partyService->getGame(),
            'optionsForm'           => $optionsForm->createView(),
            'creationFormTemplate'  => $extendedGame->getCreationFormTemplate(),
        ));
    }
    
    
    /**
     * @Route(
     *      "/games/{slugGame}/{slugParty}/preparation",
     *      name = "elcore_party_preparation"
     * )
     */
    public function prepareAction($_locale, $slugGame, $slugParty)
    {
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale, $this->container);
        
        $party = $partyService->getParty();
        
        if (!in_array($party->getState(), array(Party::PREPARATION, Party::STARTING))) {
            return $this->redirectParty($_locale, $slugGame, $slugParty);
        }
        
        $player         = $this->get('el_core.session')->getPlayer();
        $canJoin        = true === $partyService->canJoin();
        $isHost         = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        $inParty        = $partyService->inParty();
        $extendedGame   = $partyService->getExtendedGame();
        
        $this->get('el_core.js_vars')
                ->initPhaxController('slot')
                ->addContext('core-party', $party->jsonSerialize())
                ->addContext('is-host', $isHost)
                ->addContext('slug-party', $slugParty)
                ->addContext('in-party', $inParty)
                ->useTrans('open')
                ->useTrans('close')
                ->useTrans('join')
                ->useTrans('slot.open')
                ->useTrans('slot.closed')
                ->useTrans('delete.slot')
                ->useTrans('change.slot')
                ->useTrans('ban')
                ->useTrans('create.account')
                ->useTrans('invite.player')
                ->useTrans('invite.cpu')
        ;
        
        return $this->render('CoreBundle:Party:preparation.html.twig', array(
            'player'                    => $player,
            'coreParty'                 => $party,
            'extendedOptions'           => $extendedGame->loadParty($party),
            'extendedOptionsTemplate'   => $extendedGame->getDisplayOptionsTemplate(),
            'game'                      => $party->getGame(),
            'slots'                     => $party->getSlots(),
            'inParty'                   => $inParty,
            'canJoin'                   => $canJoin,
            'isHost'                    => $isHost,
        ));
    }
    
    /**
     * Controller used for url invitation
     * 
     * @Route(
     *      "/games/{slugGame}/{slugParty}/join",
     *      name = "elcore_party_join"
     * )
     */
    public function joinAction($_locale, $slugGame, $slugParty)
    {
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
        ;
        
        try {
            $partyService->join();
        } catch (ELUserException $e) {
            $e->addFlashMessage($this->get('session'));
        }
        
        return $this->redirectParty($_locale, $slugGame, $slugParty, $partyService->getParty());
    }
    
    
    /**
     * @Route(
     *      "/games/{slugGame}/{slugParty}/preparation/action",
     *      name = "elcore_party_preparation_action"
     * )
     */
    public function prepareActionAction($_locale, $slugGame, $slugParty)
    {
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale, $this->container)
        ;
        
        $player     = $this->get('el_core.session')->getPlayer();
        $party      = $partyService->getParty();
        $isHost     = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
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
                
                if ($isHost) {
                    try {
                        $partyService->start();
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
                    $partyService->join();
                } catch (ELUserException $e) {
                    $e->addFlashMessage($this->get('session'));
                }
                break;
            
            case 'remake':
                if ($party->getState() !== Party::ENDED) {
                    break;
                }
                
                $extendedGame = $this
                        ->get($partyService->getGameServiceName())
                ;
                
                $remakeParty = $this
                        ->get('el_core.party')
                        ->setPartyBySlug($slugParty, $_locale)
                        ->remake($extendedGame)
                ;
                
                return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                    '_locale'       => $_locale,
                    'slugGame'     => $remakeParty->getGame()->getSlug(),
                    'slugParty'    => $remakeParty->getSlug(),
                )));
                break;
            
            default:
                throw new ELCoreException('Unknown action : "'.$action.'"');
        }
        
        return $this->redirectParty($_locale, $slugGame, $slugParty, $party);
    }
    
    
    /**
     * Redirect on /preparation or /XXX if not active
     * 
     * @Route(
     *      "/games/{slugGame}/{slugParty}",
     *      name = "elcore_party"
     * )
     */
    public function activeAction($_locale, $slugGame, $slugParty)
    {
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
        ;
        
        $party = $partyService->getParty();
        
        if ($party->getState() !== Party::ACTIVE) {
            return $this->redirectParty($_locale, $slugGame, $slugParty, $party);
        }
        
        $extendedGame  = $this->get($partyService->getGameServiceName());
        $jsVars         = $this->get('el_core.js_vars');
        
        $jsVars
            ->initPhaxController('party')
            ->addContext('core-party', $party->jsonSerialize())
            ->addContext('extended-party', $extendedGame->loadParty($party)->jsonSerialize())
        ;
        
        return $extendedGame->activeAction($_locale, $partyService);
    }
    
    
    /**
     * @Route(
     *      "/games/{slugGame}/{slugParty}/results",
     *      name = "elcore_party_ended"
     * )
     */
    public function endedAction($_locale, $slugGame, $slugParty)
    {
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
        ;
        
        $party = $partyService->getParty();
        
        if ($party->getState() !== Party::ENDED) {
            return $this->redirectParty($_locale, $slugGame, $slugParty, $party);
        }
        
        $extendedGame = $this->get($partyService->getGameServiceName());
        
        return $extendedGame->endedAction($_locale, $partyService);
    }
    
    
    private function redirectParty($_locale, $slugGame, $slugParty, Party $party = null)
    {
        $parameters = array(
            '_locale'       => $_locale,
            'slugGame'     => $slugGame,
            'slugParty'    => $slugParty,
        );
        
        if (is_null($party)) {
            $party = $this
                    ->get('el_core.party')
                    ->setPartyBySlug($slugParty, $_locale)
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
