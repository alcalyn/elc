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
                'shortdesc' => 'Jeu Africain consistant à stocker vos graines et les graines de l\'adversaire'
                                . ' dans votre grenier le plus rapidement.',
                'longdesc'  => 'Vous et votre adversaire disposez de compartiments contenant chacun des graines.'
                                . ' Vous jouez tour par tour, et quand c\'est votre tour,'
                                . ' vous devez choisir un de vos compartiment, y prendre les graines,'
                                . ' et les déposer une à une dans les autres compartiments,'
                                . ' y compris ceux de l\'adversaire.'
                                . ' Lorsque vous déposez la dernière graine chez votre adversaire'
                                . ' et qu\'il en reste un certain nombre,'
                                . ' vous prenez les graines de son compartiment et les stockez dans votre grenier.',
                'picHome'   => 'http://a397.idata.over-blog.com/390x500/5/36/54/50/OEUVRES-ART/JEUX-TRADITIONNELS/awale2b.jpg',
            ),
            array(
                'game'      => 'awale',
                'lang'      => 'en',
                'title'     => 'Awale',
                'slug'      => 'awale',
                'shortdesc' => 'African game of storing your seeds and your opponent\'s seeds'
                                . ' in your attic, the faster you can.',
                'longdesc'  => 'You and your opponent have slots each containing seeds.'
                                . ' This is a turn-based game, and then it is your turn,'
                                . ' you have to choose a slot, take seeds,'
                                . ' and drop them one by one into the other slots,'
                                . ' including those of your opponent.'
                                . ' When you drop the last seed in one of your opponent slot'
                                . ' and it remains a defined number,'
                                . ' you take seeds out of this compartiment and store them in your appic.',
                'picHome'   => 'http://a397.idata.over-blog.com/390x500/5/36/54/50/OEUVRES-ART/JEUX-TRADITIONNELS/awale2b.jpg',
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
