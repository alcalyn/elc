<?php

namespace EL\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Exception\ELUserException;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Entity\Game;
use EL\CoreBundle\Event\PartyEvent;
use EL\CoreBundle\Services\PartyService;
use EL\CoreBundle\Form\Type\PartyType;
use EL\CoreBundle\Form\Entity\Options;
use EL\CoreBundle\Form\Type\OptionsType;

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
     * @Template
     */
    public function createAction($_locale, $slug, Request $request)
    {
        $em                     = $this->getDoctrine()->getManager();
        $partyService           = $this->get('el_core.party')->setGameBySlug($slug, $_locale, $this->container);
        $extendedGame           = $partyService->getExtendedGame();
        $coreParty              = $partyService->createParty($_locale);
        $extendedOptions        = $extendedGame->createParty();
        $extendedOptionsType    = $extendedGame->getPartyType();
        $options                = new Options($coreParty, $extendedOptions);
        $optionsForm            = $this->createForm(new OptionsType($extendedOptionsType), $options);
        
        $optionsForm->handleRequest($request);
        
        if ($optionsForm->isValid()) {
            $partyService->setParty($coreParty);
            
            $em->persist($coreParty);
            
            // Dispatch event, party created
            $eventDispatcher = $this->get('event_dispatcher');
            $event = new PartyEvent($partyService, $extendedGame, $extendedOptions);
            $eventDispatcher->dispatch(PartyEvent::PARTY_CREATED, $event);

            $em->flush();

            // redirect to preparation page
            return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                '_locale'   => $_locale,
                'slugGame'  => $slug,
                'slugParty' => $coreParty->getSlug(),
            )));
        }
        
        return array(
            'game'                  => $partyService->getGame(),
            'optionsForm'           => $optionsForm->createView(),
            'creationFormTemplate'  => $extendedGame->getCreationFormTemplate(),
            'gameLayout'            => $extendedGame->getGameLayout(),
        );
    }
    
    
    /**
     * @Route(
     *      "/games/{slugGame}/{slugParty}/preparation",
     *      name = "elcore_party_preparation"
     * )
     * @Template
     */
    public function prepareAction($_locale, $slugGame, $slugParty, PartyService $partyService)
    {
        $party              = $partyService->getParty();
        
        if (!in_array($party->getState(), array(Party::PREPARATION, Party::STARTING))) {
            return $this->redirectParty($_locale, $slugGame, $slugParty);
        }
        
        $game               = $partyService->getGame();
        $slots              = $party->getSlots();
        $player             = $this->get('el_core.session')->getPlayer();
        $canJoin            = true === $partyService->canJoin();
        $isHost             = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        $inParty            = $partyService->inParty();
        $extendedGame       = $partyService->getExtendedGame();
        $extendedOptions    = $extendedGame->loadParty($party);
        $slotsConfiguration = $extendedGame->getSlotsConfiguration($extendedOptions)['parameters'];
        $options            = $extendedGame->getDisplayOptionsTemplate($party, $extendedOptions);
        $optionsTemplate    = is_array($options) ? $options['template'] : $options ;
        $optionsVars        = is_array($options) ? $options['vars'] : array() ;
        $scoreService       = $this->get('el_core.score');
        
        $scoreService->badgePlayers($partyService->getPlayers(), $game);
        
        $this->get('el_core.js_vars')
                ->initPhaxController('party')
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
        
        return array(
            'player'                    => $player,
            'coreParty'                 => $party,
            'extendedOptions'           => $extendedOptions,
            'extendedOptionsTemplate'   => $optionsTemplate,
            'slotsConfiguration'        => $slotsConfiguration,
            'game'                      => $game,
            'slots'                     => $slots,
            'inParty'                   => $inParty,
            'canJoin'                   => $canJoin,
            'isHost'                    => $isHost,
            'gameLayout'                => $extendedGame->getGameLayout(),
        ) + $optionsVars;
    }
    
    /**
     * Controller used for url invitation
     * 
     * @Route(
     *      "/games/{slugGame}/{slugParty}/join",
     *      name = "elcore_party_join"
     * )
     */
    public function joinAction($_locale, $slugGame, $slugParty, PartyService $partyService)
    {
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
    public function prepareActionAction($_locale, $slugGame, $slugParty, PartyService $partyService, Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $player     = $this->get('el_core.session')->getPlayer();
        $party      = $partyService->getParty();
        $t          = $this->get('translator');
        $session    = $this->get('session');
        $flashbag   = $session->getFlashBag();
        $action     = $request->request->get('action');
        
        switch ($action) {
            case 'run':
                
                try {
                    // Dispatch event, party started
                    $eventDispatcher = $this->get('event_dispatcher');
                    $event = new PartyEvent($partyService);
                    $eventDispatcher->dispatch(PartyEvent::PARTY_STARTED, $event);
                } catch (ELUserException $e) {
                    $e->addFlashMessage($session);
                }
                
                $em->flush();
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
                
                $remakeParty = $partyService->remake();
                
                return $this->redirect($this->generateUrl('elcore_party_preparation', array(
                    '_locale'   => $_locale,
                    'slugGame'  => $remakeParty->getGame()->getSlug(),
                    'slugParty' => $remakeParty->getSlug(),
                )));
            
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
    public function activeAction($_locale, $slugGame, $slugParty, PartyService $partyService)
    {
        $party = $partyService->getParty();
        
        if ($party->getState() !== Party::ACTIVE) {
            return $this->redirectParty($_locale, $slugGame, $slugParty, $party);
        }
        
        $extendedGame   = $partyService->loadExtendedGame($this->container)->getExtendedGame();
        $extendedParty  = $extendedGame->loadParty($party);
        $jsVars         = $this->get('el_core.js_vars');
        
        if (!($extendedParty instanceof \JsonSerializable)) {
            throw new ELCoreException(
                    'Your class ('.get_class($extendedParty).') must implement JsonSerializable'
            );
        }
        
        $widgetService = $this->get('el_core.widgets');
        $widgetService->add('CoreBundle:Widget:currentParty', array(), 0);
        
        $jsVars
            ->initPhaxController('party')
            ->addContext('core-party', $party->jsonSerialize())
            ->addContext('extended-party', $extendedParty->jsonSerialize())
        ;
        
        return $extendedGame->activeAction($_locale, $partyService, $extendedParty);
    }
    
    
    /**
     * @Route(
     *      "/games/{slugGame}/{slugParty}/results",
     *      name = "elcore_party_ended"
     * )
     */
    public function endedAction($_locale, $slugGame, $slugParty, PartyService $partyService)
    {
        $party = $partyService->getParty();
        
        if ($party->getState() !== Party::ENDED) {
            return $this->redirectParty($_locale, $slugGame, $slugParty, $party);
        }
        
        $this->get('el_core.js_vars')
            ->initPhaxController('party')
            ->addContext('core-party', $party->jsonSerialize())
            ->useTrans('has.remake')
        ;
        
        $extendedGame = $partyService->loadExtendedGame($this->container)->getExtendedGame();
        
        return $extendedGame->endedAction($_locale, $partyService, $extendedGame);
    }
    
    /**
     * Return the redirect response to the good game screen (prepare, active, ended)
     * 
     * @param string $_locale
     * @param string $slugGame
     * @param string $slugParty
     * @param \EL\CoreBundle\Entity\Party $party
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * 
     * @throws ELCoreException
     */
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
                    ->setPartyBySlug($slugParty, $slugGame, $_locale)
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
