<?php

namespace EL\ELCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use EL\ELCoreBundle\Entity\Game;
use EL\ELCoreBundle\Entity\GameVariant;

/**
 * GameVariantRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GameVariantRepository extends EntityRepository
{
    public function get(Game $game, $variant_name)
    {
        return $this->_em->createQuery('
            select gv
            from ELCoreBundle:GameVariant gv
            left join gv.game g
            where g.id = :game_id
            and gv.name = :variant_name
        ')->setParameters(array(
            'game_id'       => $game->getId(),
            'variant_name'  => $variant_name,
        ))
        ->getOneOrNullResult();
    }
}
