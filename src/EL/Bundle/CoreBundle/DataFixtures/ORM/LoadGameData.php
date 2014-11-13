<?php

namespace EL\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\Bundle\CoreBundle\Entity\Game;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $items = array(
            array(
                'name'              => 'chess',
                'nbplayermin'       => 2,
                'nbplayermax'       => 2,
                'visible'           => false,
                'category'          => 'strategy',
                'rankingColumns'    => 'parties,wins,losses,draws,ratio,elo,points',
                'rankingOrder'      => 'wins:d,draws:d',
                'rankingReference'  => 'wins',
            ),
            array(
                'name'              => 'not.visible',
                'nbplayermin'       => 2,
                'nbplayermax'       => 8,
                'visible'           => false,
                'category'          => 'casino',
                'rankingColumns'    => 'parties,wins,losses,draws,ratio,elo,points',
                'rankingOrder'      => 'wins:d,draws:d',
                'rankingReference'  => 'wins',
            ),
        );
        
        $objects = array();
        
        $i = 0;
        foreach ($items as $item) {
            $object = new Game();
            
            $object
                    ->setName($item['name'])
                    ->setNbplayerMin($item['nbplayermin'])
                    ->setNbplayerMax($item['nbplayermax'])
                    ->setVisible($item['visible'])
                    ->setCategory($this->getReference($item['category']))
                    ->setRankingColumns($item['rankingColumns'])
                    ->setRankingOrder($item['rankingOrder'])
                    ->setRankingReference($item['rankingReference'])
            ;
            
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
