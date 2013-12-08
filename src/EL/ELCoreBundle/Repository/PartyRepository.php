<?php

namespace EL\ELCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PartyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartyRepository extends EntityRepository
{
    public function findByLang($locale, $slug)
    {
        $query = $this->_em->createQuery('
            select p, g, gl, s
            from ELCoreBundle:Party p
            join p.slots s
            join p.game g
            join p.host h
            join g.langs gl
            join gl.lang l
            where  l.locale = :locale
            and p.slug = :slug
            order by s.position
        ')->setParameters(array(
            'locale'    => $locale,
            'slug'      => $slug,
        ));
        
        return $query->getSingleResult();
    }
}
