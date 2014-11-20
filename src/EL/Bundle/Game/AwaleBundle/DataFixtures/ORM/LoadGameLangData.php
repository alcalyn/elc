<?php

namespace EL\Bundle\Game\AwaleBundle\DataFixtures\ORM;

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
                'game'      => 'awale',
                'lang'      => 'fr',
                'title'     => 'Awalé',
                'slug'      => 'awale',
                'shortdesc' => 'Jeu Africain consistant à capturer le plus de graines, ainsi que celles de votre'
                                . ' adversaire dans votre grenier.',
                'longdesc'  => 'Dans l\'Awalé (ou Awélé), vous jouez tour par tour'
                                . ' et possédez des graines.'."\n"
                                . ' À chaque tour,'
                                . ' vous prenez les graines d\'un de vos compartiments,'
                                . ' et les distribuez une à une dans les autres compartiments,'
                                . ' y compris ceux de l\'adversaire.'."\n"
                                . ' Lorsque vous déposez la dernière chez votre adversaire'
                                . ' et qu\'il en reste 2 ou 3,'
                                . ' vous prenez les graines de son compartiment'
                                . ' et les stockez dans votre grenier.'."\n"
                                . ' Capturez-en le plus !',
                'picHome'   => 'http://a397.idata.over-blog.com/390x500/5/36/54/50/OEUVRES-ART/JEUX-TRADITIONNELS/awale2b.jpg',
            ),
            array(
                'game'      => 'awale',
                'lang'      => 'en',
                'title'     => 'Oware',
                'slug'      => 'oware',
                'shortdesc' => 'African game of capturing your seeds and those your opponent'
                                . ' in your attic, the faster you can.',
                'longdesc'  => 'Oware game (also named Awale) is a turn-based game.'
                                . ' You and your opponent have seeds.'."\n"
                                . ' You have to choose a few and distribute them to you and your opponent.'."\n"
                                . ' When you drop the last seed in a container of your opponent'
                                . ' and remains 2 or 3,'
                                . ' you capture and store these seeds in your attic.'."\n"
                                . ' Capture them all !',
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
