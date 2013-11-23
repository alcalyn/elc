<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Form\Entity\PartyOptions;
use EL\ElCoreBundle\Form\Type\PartyOptionsType;
use EL\ELAbstractGameBundle\Form\Entity\SpecialPartyOptions;
use EL\ElAbstractGameBundle\Form\Type\SpecialPartyOptionsType;

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
        
        
        $party_service = $this->get('el_core.party');
        
        $party_options = new PartyOptions();
        
        $party_options_form = $this->createForm(new PartyOptionsType($party_service), $party_options);
        
        $party_options_form->handleRequest($this->getRequest());
        
        if ($party_options_form->isValid()) {
            
        }
        
        return $this->render('ELCoreBundle:Games:creation.html.twig', array(
            'game'             => $game,
            'party_options'    => $party_options_form->createView(),
        ));
    }
    
    
    
    /**
     * @Route(
     *      "/games/{slug}/preparation",
     *      name = "elcore_game_preparation"
     * )
     */
    public function prepareAction($_locale, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $game = $em
                ->getRepository('ELCoreBundle:Game')
                ->findByLang($_locale, $slug);
        
        return $this->render('ELCoreBundle:Games:preparation.html.twig', array(
            'game' => $game,
        ));
    }
    

}
