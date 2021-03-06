<?php

namespace EL\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SlotRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SlotRepository extends EntityRepository
{
    public function findOneByPlayerAndParty($playerId, $partyId)
    {
        $query = $this->_em->createQuery(
            '
                select s
                from CoreBundle:Slot s
                join s.player player
                join s.party party
                where player.id = :playerId
                and party.id = :partyId
            '
        )
        ->setParameters(array(
            'playerId' => $playerId,
            'partyId'  => $partyId,
        ));
        
        return $query->getSingleResult();
    }
}
