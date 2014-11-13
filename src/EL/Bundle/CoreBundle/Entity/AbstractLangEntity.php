<?php

namespace EL\Bundle\CoreBundle\Entity;

/**
 * class AbstractLangEntity
 * 
 * To be extended by classes which use translation.
 * Then you can call translated attribute though main class.
 * 
 * Example :
 *      $game->title returns $game->langs[0]->getTitle()
 */
class AbstractLangEntity
{
    /**
     * @var array
     */
    protected $langs;
    
    /**
     * Call getter of first localized entity named $name
     * 
     * @param string $name
     * @param array $args
     * 
     * @return mixed
     * 
     * @throws \Exception if getter not found
     */
    public function __call($name, $args)
    {
        if (strtolower(substr($name, 0, 3)) === 'get') {
            $getter = ucfirst($name);
        } else {
            $getter = 'get'.ucfirst($name);
        }
        
        if (method_exists($this->langs[0], $getter)) {
            return $this->langs[0]->$getter();
        } else {
            throw new \Exception(sprintf(
                'Method "%s" for object "%s" does not exist',
                $getter,
                get_class($this)
            ));
        }
    }
}
