<?php

namespace EL\ChatBundle\Controller;

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
    public function generalAction($_locale)
    {
        $topicName = 'general-'.$_locale;
        $title = $this->get('translator')->trans('chat.general');
        
        $this->get('el_core.js_vars')->useTrans('chat.you.quit');
        
        return array(
            'title' => $title,
            'topicName' => $topicName,
        );
    }
}
