<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EL\CoreBundle\Entity\WLD;

class FrontController extends Controller
{
    /**
     * @Route(
     *      "/",
     *      name = "elcore_home",
     *      requirements = {
     *          "_scheme" = "http"
     *      }
     * )
     */
    public function indexAction($_locale)
    {
        return $this->render('CoreBundle:Front:index.'.$_locale.'.html.twig', array(
            //'slideshow' => true,
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
        return $this->render('CoreBundle:Front:about.'.$_locale.'.html.twig');
    }
    
    /**
     * @Route(
     *      "/faq",
     *      name = "elcore_faq"
     * )
     */
    public function faqAction($_locale)
    {
        return $this->render('CoreBundle:Front:faq.'.$_locale.'.html.twig');
    }
}
