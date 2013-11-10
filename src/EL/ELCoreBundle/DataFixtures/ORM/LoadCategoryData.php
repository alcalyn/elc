<?php

namespace EL\ELCoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use \EL\ELCoreBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $items = array(
            array(
                'name' => 'strategy',
            ),
            array(
                'name' => 'casino',
            )
        );
        
        $i = 0;
        foreach ($items as $item) {
            $object = new Category();
            
            $object->setName($item['name']);
            
            $manager->persist($objects[$i++] = $object);
            
            $this->addReference($item['name'], $object);
        }
        
        $manager->flush();
    }
    
    
    
    public function getOrder()
    {
        return 1;
    }
}