<?php

namespace EL\ELAbstractGameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ELAbstractGameBundle:Default:index.html.twig', array('name' => $name));
    }
}
