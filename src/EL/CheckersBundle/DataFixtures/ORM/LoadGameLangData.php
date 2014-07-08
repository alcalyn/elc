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
                'shortdesc' => 'Jeu de dames, capturez tous les pions de votre adversaire.',
                'longdesc'  => 'Vous et votre adversaire disposez de pions, vous devez vous déplacer en diagonale,'
                                . ' et sauter par dessus les pions de votre adversaire pour les capturer.'."\n"
                                . ' Le jeu est disponible dans toutes les variantes existante possible :'
                                . ' Anglaise, Française/Internationale, Italienne...'."\n"
                                . ' Vous pouvez même créer une variante personnalisée,'
                                . ' jouer sur un plateau à 16 cases,'."\n"
                                . 'ou jouer avec le "Souffler n\'est pas joué" !',
                'picHome'   => 'bundles/checkers/img/home.jpg',
            ),
            array(
                'game'      => 'checkers',
                'lang'      => 'en',
                'title'     => 'Checkers',
                'slug'      => 'checkers',
                'shortdesc' => 'Checkers or Draughts, capture all your opponent pieces.',
                'longdesc'  => 'You and your opponent have pieces, you have to move them diagonally,'
                                . ' and jump over your opponent pieces to capture them.'."\n"
                                . ' The game is available in every existing variants:'
                                . ' English, French/International, Italian...'."\n"
                                . ' You can also create a personalized variant,'
                                . ' play on a 16-squares board, or huff your opponent pieces !',
                'picHome'   => 'bundles/checkers/img/home.jpg',
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
