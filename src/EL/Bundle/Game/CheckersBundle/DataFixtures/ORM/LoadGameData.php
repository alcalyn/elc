<?php

namespace EL\Bundle\Game\CheckersBundle\DataFixtures\ORM;

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
                'name'              => 'checkers',
                'nbplayermin'       => 2,
                'nbplayermax'       => 2,
                'visible'           => true,
                'category'          => 'strategy',
                'rankingColumns'    => 'parties,wins,losses,draws,ratio,elo',
                'rankingOrder'      => 'elo:d',
                'rankingReference'  => 'elo',
            ),
        );
        
        
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
