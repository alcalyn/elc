<?php

namespace EL\Bundle\CoreBundle\Controller;

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
                ->getRepository('Core:Lang')
                ->findAllExcept($_locale)
        ;
        
        return array(
            'langs' => $langs,
        );
    }
}