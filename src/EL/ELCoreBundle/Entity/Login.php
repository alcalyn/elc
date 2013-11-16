<?php

namespace EL\ELCoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class Login {
    
    /**
     *
     * @var string
     * @Assert\Length(
     *      min = "2",
     *      max = "31",
     *      minMessage = "Votre pseudo doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre pseudo ne peut pas être plus long que {{ limit }} caractères"
     * )
     */
    private $pseudo;
    
    
    /**
     *
     * @var string
     * @Assert\NotBlank()
     */
    private $password;
    
    
    /**
     *
     * @var boolean 
     */
    private $remember_me;
    
    
    
    public function getPseudo()
    {
        return $this->pseudo;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRememberMe()
    {
        return $this->remember_me;
    }

    
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setRememberMe($remember_me)
    {
        $this->remember_me = $remember_me;
        return $this;
    }


    
}
