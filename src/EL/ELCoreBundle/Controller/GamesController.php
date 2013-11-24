<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Form\Entity\PartyOptions;
use EL\ElCoreBundle\Form\Type\PartyOptionsType;

class GamesController extends Controller
{
    /**
     * @Route(
     *      "/games",
     *      name = "elcore_games_list"
     * )
     */
    public function listAction($_locale)
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em
                ->getRepository('ELCoreBundle:Game')
                ->findAllByLang($_locale);

        return $this->render('ELCoreBundle:Games:list.html.twig', array(
            'games' => $games,
        ));
    }


    /**
     * @Route(
     *      "/games/{slug}",
     *      name = "elcore_game_home"
     * )
     */
    public function homeAction($_locale, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $game = $em
                ->getRepository('ELCoreBundle:Game')
                ->findByLang($_locale, $slug);
        
        return $this->render('ELCoreBundle:Games:home.html.twig', array(
            'game' => $game,
        ));
    }
    
    
    
    /**
     * @Route(
     *      "/games/{slug}/creation",
     *      name = "elcore_game_creation"
     * )
     */
    public function createAction($_locale, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $game = $em
                ->getRepository('ELCoreBundle:Game')
                ->findByLang($_locale, $slug);
        
        
        $party_service = $this
                ->get('el_core.party')
                ->setGame($game);
        
        $core_game_service = $this
                ->get('el_core.game')
                ->setGame($game);
        
        $game_service = $this
                ->get($core_game_service->getGameServiceName());
        
        $party_options = new PartyOptions();
        $party_options
                ->setTitle($party_service->generateRandomTitle())
                ->setSpecialPartyOptions($game_service->getOptions());
        
        $party_options_form = $this->createForm(new PartyOptionsType($game_service->getOptionsType()), $party_options);
        
        $party_options_form->handleRequest($this->getRequest());
        
        if ($party_options_form->isValid()) {
            $party = $party_service->createParty($party_options->getTitle(), !$party_options->getPrivate());
            
            return $this->redirect($this->generateUrl('elcore_game_preparation', array(
                '_locale'       => $_locale,
                'slug_game'     => $slug,
                'slug_party'    => $party->getSlug(),
            )));
        }
        
        return $this->render('ELCoreBundle:Games:creation.html.twig', array(
            'game'             => $game,
            'party_options'    => $party_options_form->createView(),
        ));
    }
    
    
    
    /**
     * @Route(
     *      "/games/{slug_game}/{slug_party}/preparation",
     *      name = "elcore_game_preparation"
     * )
     */
    public function prepareAction($_locale, $slug_game, $slug_party)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('ELCoreBundle:Party')
                ->findByLang($_locale, $slug_party);
        
        return $this->render('ELCoreBundle:Games:preparation.html.twig', array(
            'party'     => $party,
            'game'      => $party->getGame(),
        ));
    }
    

}
