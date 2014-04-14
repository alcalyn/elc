<?php

namespace EL\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EL\CoreBundle\Entity\GameLang;

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
            array(
                'game'      => 'tictactoe',
                'lang'      => 'fr',
                'title'     => 'Morpion',
                'slug'      => 'morpion',
                'shortdesc' => "Pas envie de réfléchir\xC2\xA0? Faîtes quelques parties de Morpion\xC2\xA0!",
                'longdesc'  => 'Sûrement le jeu le plus basique du monde, alignez 3 symboles avant votre adversaire'
                                . ' et remportez la partie.',
                'picHome'   => 'bundles/tictactoe/img/pictureHome.jpg',
            ),
            array(
                'game'      => 'tictactoe',
                'lang'      => 'en',
                'title'     => 'Tic Tac Toe',
                'slug'      => 'tictactoe',
                'shortdesc' => "Don't want to take your head\xC2\xA0? Make some Tic tac toe games\xC2\xA0!",
                'longdesc'  => 'Certainly the most basic game of the world, align 3 symbols before your opponent'
                                . ' and win the game.',
                'picHome'   => 'bundles/tictactoe/img/pictureHome.jpg',
            ),
            array(
                'game'      => 'awale',
                'lang'      => 'fr',
                'title'     => 'Awalé',
                'slug'      => 'awale',
                'shortdesc' => 'Awalé desc courte',
                'longdesc'  => 'Awalé desc longue',
                'picHome'   => null,
            ),
            array(
                'game'      => 'awale',
                'lang'      => 'en',
                'title'     => 'Awale',
                'slug'      => 'awale',
                'shortdesc' => 'Awale short desc',
                'longdesc'  => 'Awale long desc',
                'picHome'   => null,
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
