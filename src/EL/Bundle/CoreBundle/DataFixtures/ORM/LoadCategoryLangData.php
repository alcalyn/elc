<?php

namespace EL\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\Bundle\CoreBundle\Entity\CategoryLang;

class LoadCategoryLangData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $items = array(
            array(
                'lang'      => 'fr',
                'category'  => 'strategy',
                'title'     => 'Stratégie',
            ),
            array(
                'lang'      => 'en',
                'category'  => 'strategy',
                'title'     => 'Strategy',
            ),
            array(
                'lang'      => 'fr',
                'category'  => 'casino',
                'title'     => 'Casino',
            ),
            array(
                'lang'      => 'en',
                'category'  => 'casino',
                'title'     => 'Casino',
            ),
        );
        
        $objects = array();
        
        $i = 0;
        foreach ($items as $item) {
            $object = new CategoryLang();
            
            $object
                    ->setCategory($this->getReference($item['category']))
                    ->setLang($this->getReference($item['lang']))
                    ->setTitle($item['title'])
            ;
            
            $manager->persist($objects[$i++] = $object);
        }
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
}
