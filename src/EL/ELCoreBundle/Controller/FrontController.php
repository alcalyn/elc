<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class FrontController extends Controller
{
    /**
     * @Route(
     *      "/",
     *      name = "elcore_home"
     * )
     */
    public function indexAction($_locale)
    {
        return $this->render('ELCoreBundle:Front:index.'.$_locale.'.html.twig', array(
            'slideshow' => true,
            'player'    => $this->player,
        ));
    }
    
    /**
     * @Route(
     *      "/about",
     *      name = "elcore_about"
     * )
     */
    public function aboutAction($_locale)
    {
        return $this->render('ELCoreBundle:Front:about.'.$_locale.'.html.twig');
    }
    
    /**
     * @Route(
     *      "/faq",
     *      name = "elcore_faq"
     * )
     */
    public function faqAction($_locale)
    {
        return $this->render('ELCoreBundle:Front:faq.'.$_locale.'.html.twig');
    }
}