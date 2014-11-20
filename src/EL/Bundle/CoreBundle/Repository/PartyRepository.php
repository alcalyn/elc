<?php

namespace EL\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use EL\Core\Entity\Party;
use EL\Core\Entity\Player;

/**
 * PartyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartyRepository extends EntityRepository
{
    public function findByLang($locale, $slugParty, $slugGame)
    {
        $query = $this->_em->createQuery(
            '
                select p, g, gl, s, pl
                from Core:Party p
                left join p.slots s
                left join s.player pl
                left join p.game g
                left join p.host h
                left join g.langs gl
                left join gl.lang l
                where l.locale = :locale
                and  p.slug = :slugParty
                and gl.slug = :slugGame
                order by s.position
            '
        )->setParameters(array(
            'locale'    => $locale,
            'slugParty' => $slugParty,
            'slugGame'  => $slugGame,
        ));
        
        return $query->getSingleResult();
    }
    
    /**
     * @param type $locale
     * @param type $player
     * @return type
     */
    public function findCurrentPartiesForPlayer($locale, Player $player)
    {
        return $this->_em->createQuery(
            '
                select p, g, gl, s, pl
                from Core:Party p
                left join p.slots _s
                left join _s.player _pl
                left join p.slots s
                left join s.player pl
                left join p.game g
                left join g.langs gl
                left join gl.lang l
                where l.locale = :locale
                and _pl.id = :playerId
                and p.state in (
                    :state_preparation,
                    :state_starting,
                    :state_active
                )
                order by s.position
            '
        )->setParameters(array(
            'locale'            => $locale,
            'playerId'          => $player->getId(),
            'state_preparation' => Party::PREPARATION,
            'state_starting'    => Party::STARTING,
            'state_active'      => Party::ACTIVE,
        ))->getResult();
    }
    
    public function findPlayersInRemakeParty($oldParty)
    {
        return $this->_em->createQueryBuilder()
                ->select('p, pr, s, pl')
                ->from('Core:Party', 'p')
                ->leftJoin('p.remake', 'pr')
                ->leftJoin('pr.slots', 's')
                ->leftJoin('s.player', 'pl')
                ->where('p = :oldParty')
                ->setParameters(array(
                    ':oldParty' => $oldParty,
                ))
                ->getQuery()
                ->getOneOrNullResult()
        ;
                
    }
    
    public function countSlug($slug)
    {
        $query = $this->_em->createQuery(
            '
                select count(p)
                from Core:Party p
                where  p.slug = :slug
            '
        )->setParameters(array(
            'slug' => $slug,
        ));
        
        return $query->getSingleScalarResult();
    }
}