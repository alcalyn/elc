<?php

namespace EL\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use EL\CoreBundle\Entity\Player;
use EL\CoreBundle\Entity\GameVariant;

/**
 * ScoreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ScoreRepository extends EntityRepository
{
    public function get(Player $player, GameVariant $gameVariant)
    {
        return $this->_em->createQuery(
            '
                select s
                from CoreBundle:Score s
                left join s.player p
                left join s.gameVariant gv
                where p.id = :playerId
                and gv.id = :gameVariantId
            '
        )->setParameters(array(
            'playerId'         => $player->getId(),
            'gameVariantId'   => $gameVariant->getId(),
        ))->getOneOrNullResult();
    }
    
    public function getRanking(GameVariant $gameVariant, array $order = array(), $length = -1, $offset = 0)
    {
        $query = $this->_em->createQueryBuilder()
                ->select('s, p')
                ->from('CoreBundle:Score', 's')
                ->leftJoin('s.gameVariant', 'gv')
                ->leftJoin('s.player', 'p')
                ->where('gv.id = :gameVariantId')
        ;
        
        foreach ($order as $field => $direction) {
            switch ($field) {
                case 'ratio':
                    $query->addOrderBy('s.wins / (0.5 * ((s.losses + 1) + ABS(s.losses - 1)))', $direction);
                    break;
                
                case 'score':
                    $query->addOrderBy('s.points', $direction);
                    break;
                
                case 'parties':
                    $query->addOrderBy('s.wins + s.losses + s.draws', $direction);
                    break;
                
                default:
                    $query->addOrderBy('s.'.$field, $direction);
            }
        }
        
        if ($length >= 0) {
            $query->setFirstResult($offset);
            $query->setMaxResults($length);
        }
        
        $query->setParameters(array(
            'gameVariantId' => $gameVariant->getId(),
        ));
        
        return $query->getQuery()->getResult();
    }
}
