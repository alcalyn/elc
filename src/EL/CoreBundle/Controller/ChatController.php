<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ChatController extends Controller
{
    /**
     * @Route(
     *      "/chat/general",
     *      name = "elcore_chat",
     *      requirements = {
     *          "_scheme" = "http",
     *          "_method" = "GET"
     *      }
     * )
     * @Template
     */
    public function generalAction()
    {
        return array(
        );
    }
}
