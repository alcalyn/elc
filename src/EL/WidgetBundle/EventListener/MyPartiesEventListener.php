<?php

namespace EL\WidgetBundle\EventListener;

use EL\CoreBundle\Event\PartyEvent;

class MyPartiesEventListener
{
    public function onPartyStartAfter(PartyEvent $event)
    {
        echo 'party started';
    }
}
