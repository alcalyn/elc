<?php

namespace EL\ELCoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\ELCoreBundle\Entity\GameLang;

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
            ),
            array(
                'game'      => 'chess',
                'lang'      => 'en',
                'title'     => 'Chess',
                'slug'      => 'chess',
                'shortdesc' => 'Chess short desc',
                'longdesc'  => 'Chess long desc',
            ),
            array(
                'game'      => 'tictactoe',
                'lang'      => 'fr',
                'title'     => 'Morpion',
                'slug'      => 'morpion',
                'shortdesc' => 'Morpion desc courte',
                'longdesc'  => 'Morpion desc longue',
            ),
            array(
                'game'      => 'tictactoe',
                'lang'      => 'en',
                'title'     => 'Tic Tac Toe',
                'slug'      => 'tictactoe',
                'shortdesc' => 'Tic tac toe short desc',
                'longdesc'  => 'Tic tac toe long desc',
            ),
            array(
                'game'      => 'awale',
                'lang'      => 'fr',
                'title'     => 'Awalé',
                'slug'      => 'awale',
                'shortdesc' => 'Awalé desc courte',
                'longdesc'  => 'Awalé desc longue',
            ),
            array(
                'game'      => 'awale',
                'lang'      => 'en',
                'title'     => 'Awale',
                'slug'      => 'awale',
                'shortdesc' => 'Awale short desc',
                'longdesc'  => 'Awale long desc',
            ),
        );
        
        
        $i = 0;
        foreach ($items as $item) {
            $object = new GameLang();
            
            $object->setGame($this->getReference($item['game']));
            $object->setLang($this->getReference($item['lang']));
            $object->setTitle($item['title']);
            $object->setSlug($item['slug']);
            $object->setShortDesc($item['shortdesc']);
            $object->setLongDesc($item['longdesc']);
            
            $manager->persist($objects[$i++] = $object);
        }
        
        $manager->flush();
    }
    
    
    public function getOrder()
    {
        return 3;
    }
}