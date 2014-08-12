<?php

namespace EL\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PlayerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends EntityRepository
{
    public function pseudoCount($pseudo)
    {
        return $this->_em->createQuery(
            '
                select count(p.id)
                from CoreBundle:Player p
                where p.pseudo = :pseudo
                and p.invited = 0
            '
        )
        ->setParameters(array(
            'pseudo' => $pseudo,
        ))
        ->getSingleScalarResult();
    }
    
    public function loginQuery($pseudo, $passwordHash)
    {
        return $this->_em
                ->getRepository('CoreBundle:Player')
                ->findBy(array(
                    'invited'       => 0,
                    'bot'           => 0,
                    'pseudo'        => $pseudo,
                    'passwordHash'  => $passwordHash,
                ));
    }
    
    public function getPlayersByPseudoCI($pseudo)
    {
        return $this->_em
            ->createQuery(
                '
                    select p
                    from CoreBundle:Player p
                    where lower(p.pseudo) like :pseudo
                '
            )
            ->setParameters(array(
                'pseudo' => '%'.strtolower($pseudo).'%',
            ))
            ->getResult()
        ;
    }
    
    public function getPlayerByPseudoCI($pseudo)
    {
        return $this->_em
            ->createQuery(
                '
                    select p
                    from CoreBundle:Player p
                    where lower(p.pseudo) like :pseudo
                '
            )
            ->setParameters(array(
                'pseudo' => strtolower($pseudo),
            ))
            ->getOneOrNullResult()
        ;
    }
}
