<?php

namespace EL\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\CoreBundle\Entity\Game;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $items = array(
            array(
                'name'          => 'chess',
                'nbplayermin'   => 2,
                'nbplayermax'   => 2,
                'visible'       => true,
                'category'       => 'strategy',
            ),
            array(
                'name'          => 'tictactoe',
                'nbplayermin'   => 2,
                'nbplayermax'   => 4,
                'visible'       => true,
                'category'       => 'strategy',
            ),
            array(
                'name'          => 'awale',
                'nbplayermin'   => 2,
                'nbplayermax'   => 2,
                'visible'       => true,
                'category'       => 'casino',
            ),
        );
        
        
        $i = 0;
        foreach ($items as $item) {
            $object = new Game();
            
            $object->setName($item['name']);
            $object->setNbplayerMin($item['nbplayermin']);
            $object->setNbplayerMax($item['nbplayermax']);
            $object->setVisible($item['visible']);
            $object->setCategory($this->getReference($item['category']));
            
            $manager->persist($objects[$i++] = $object);
            
            $this->addReference($item['name'], $object);
        }
        
        $manager->flush();
    }
    
    
    public function getOrder()
    {
        return 2;
    }
}