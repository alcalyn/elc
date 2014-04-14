<?php

namespace EL\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LangController extends Controller
{
    /**
     * Lang switcher
     * 
     * @Template
     */
    public function switcherAction($_locale)
    {
        $em = $this->getDoctrine()->getManager();
        
        $langs = $em
                ->getRepository('CoreBundle:Lang')
                ->findAllExcept($_locale)
        ;
        
        return array(
            'langs' => $langs,
        );
    }
}
