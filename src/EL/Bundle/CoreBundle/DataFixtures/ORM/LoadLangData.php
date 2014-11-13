<?php

namespace EL\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\Bundle\CoreBundle\Entity\Lang;

class LoadLangData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $items = array(
            array(
                'locale'    => 'en',
                'title'     => 'English',
            ),
            array(
                'locale'    => 'fr',
                'title'     => 'Français',
            ),
        );
        
        $objects = array();
        
        $i = 0;
        foreach ($items as $item) {
            $object = new Lang();
            
            $object->setLocale($item['locale']);
            $object->setTitle($item['title']);
            
            $manager->persist($objects[$i++] = $object);
            
            $this->addReference($item['locale'], $object);
        }
        
        $manager->flush();
    }
    
    
    public function getOrder()
    {
        return 1;
    }
}
