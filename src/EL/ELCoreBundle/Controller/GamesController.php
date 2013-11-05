<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GamesController extends Controller
{
    /**
     * @Route(
     *      "/en/games",
     *      defaults = {"_locale": "en"},
     *      name = "elcore_en_games_list"
     * )
     * @Route(
     *      "/fr/jeux",
     *      defaults = {"_locale": "fr"},
     *      name = "elcore_fr_games_list"
     * )
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $games = $em
                ->getRepository('ELCoreBundle:Game')
                ->findAll();
        
        return $this->render('ELCoreBundle:Games:list.html.twig', array(
            'games' => $games,
        ));
    }

}
