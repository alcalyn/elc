<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SlideshowController extends Controller
{
    
    public function indexAction()
    {
        $slides = array(
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
            'ELCoreBundle:Slideshow:slide1.html.twig',
        );
        
        return $this->render('ELCoreBundle:Slideshow:slideshow.html.twig', array(
            'slides'    => $slides,
        ));
    }
    
}
