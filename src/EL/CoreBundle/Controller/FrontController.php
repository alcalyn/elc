<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FrontController extends Controller
{
    /**
     * @Route(
     *      "/",
     *      name = "elcore_home",
     *      requirements = {
     *          "_scheme" = "http",
     *          "_method" = "GET"
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
     *      name = "elcore_about",
     *      requirements = {
     *          "_method" = "GET"
     *      }
     * )
     */
    public function aboutAction($_locale)
    {
        return $this->render('CoreBundle:Front:about.'.$_locale.'.html.twig');
    }
    
    /**
     * @Route(
     *      "/faq",
     *      name = "elcore_faq",
     *      requirements = {
     *          "_method" = "GET"
     *      }
     * )
     */
    public function faqAction($_locale)
    {
        return $this->render('CoreBundle:Front:faq.'.$_locale.'.html.twig');
    }
}
