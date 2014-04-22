<?php

namespace EL\CheckersBundle\DataFixtures\ORM;

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
                'game'      => 'checkers',
                'lang'      => 'fr',
                'title'     => 'Dames',
                'slug'      => 'dames',
                'shortdesc' => 'Jeu de dames classique, mangez tous les pions de votre adversaire.',
                'longdesc'  => 'Vous et votre adversaire disposez de pions, vous devez vous déplacer en diagonale,'
                                . ' et sautez par dessus les pions de votre adversaire pour les manger.'."\n"
                                . ' Le jeu est disponible dans toutes les versions possible : Anglaise, Française...',
                'picHome'   => '',
            ),
            array(
                'game'      => 'checkers',
                'lang'      => 'en',
                'title'     => 'Checkers',
                'slug'      => 'checkers',
                'shortdesc' => 'Classic checkers, eat all pawns of your opponent.',
                'longdesc'  => 'You and your opponent have pawns, you have to move them diagonally,'
                                . ' and jump over pawns of your opponent to move them.'."\n"
                                . ' The game is playable in every existing versions: English, French...',
                'picHome'   => '',
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
