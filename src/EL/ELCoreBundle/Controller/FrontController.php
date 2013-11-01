<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class FrontController extends Controller
{
    /**
     * @Route(
     *      "/{_locale}",
     *      defaults = {"_locale": "en"},
     *      name = "elcore_home"
     * )
     */
    public function indexAction($_locale)
    {
        $session = $this->get('session');
        $session->start();
        return $this->render('ELCoreBundle:Front:index.'.$_locale.'.html.twig');
    }
    
    /**
     * @Route(
     *      "/en/about",
     *      name = "elcore_en_about",
     *      defaults = {"_locale": "en"}
     * )
     * @Route(
     *      "/fr/a-propos",
     *      name = "elcore_fr_about",
     *      defaults = {"_locale": "fr"}
     * )
     */
    public function aboutAction($_locale)
    {
        return $this->render('ELCoreBundle:Front:about.'.$_locale.'.html.twig');
    }
}