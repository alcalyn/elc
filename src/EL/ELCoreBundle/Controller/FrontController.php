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
     *      name = "el_core_home"
     * )
     */
    public function indexAction()
    {
        return $this->render('ELCoreBundle:Front:index.html.twig');
    }
}