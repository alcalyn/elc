<?php

namespace EL\ELCoreBundle\Services;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use EL\ELCoreBundle\Entity\Player;

class SecurityService
implements
        UserProviderInterface,
        PasswordEncoderInterface
{
    
    private $em;
    
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    /*
     * Implementation of UserProviderInterface
     */
    
    public function loadUserByUsername($username)
    {
        $player = $this->em
                ->getRepository('ELCoreBundle:Player')
                ->findOneByPseudo($username);
        
        if ($player) {
            return $player;
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Player) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'EL\ELCoreBundle\Entity\Player';
    }
    
    
    /*
     * Implementation of PasswordEncoderInterface
     */
    
    public function encodePassword($raw, $salt) {
        $hash = md5($salt.$raw.$salt);
        var_dump($hash);
        return $hash;
    }
    
    public function isPasswordValid($encoded, $raw, $salt) {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}
