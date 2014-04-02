<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SlideshowController extends Controller
{
    /**
     * Initialize slideshow
     * 
     * @return array
     * 
     * @Template
     */
    public function indexAction()
    {
        $slides = array(
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
            'CoreBundle:Slideshow:slide1.html.twig',
        );
        
        return array(
            'slides'    => $slides,
        );
    }
}
