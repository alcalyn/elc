<?php

namespace EL\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\Core\Entity\GameLang;

class LoadGameLangData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $items = array(
            array(
                'game'      => 'chess',
                'lang'      => 'fr',
                'title'     => 'Echecs',
                'slug'      => 'echecs',
                'shortdesc' => 'Echecs desc courte',
                'longdesc'  => 'Echecs desc longue',
                'picHome'   => null,
            ),
            array(
                'game'      => 'chess',
                'lang'      => 'en',
                'title'     => 'Chess',
                'slug'      => 'chess',
                'shortdesc' => 'Chess short desc',
                'longdesc'  => 'Chess long desc',
                'picHome'   => null,
            ),
        );
        
        $objects = array();
        
        $i = 0;
        foreach ($items as $item) {
            $object = new GameLang();
            
            $object->setGame($this->getReference($item['game']));
            $object->setLang($this->getReference($item['lang']));
            $object->setTitle($item['title']);
            $object->setSlug($item['slug']);
            $object->setShortDesc($item['shortdesc']);
            $object->setLongDesc($item['longdesc']);
            $object->setPictureHome($item['picHome']);
            
            $manager->persist($objects[$i++] = $object);
        }
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3;
    }
}
