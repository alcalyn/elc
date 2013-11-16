<?php

namespace EL\ELCoreBundle\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class Signup {
    
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
     * @var string
     * @Assert\NotBlank()
     */
    private $password_repeat;
    
    
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

    public function getPasswordRepeat()
    {
        return $this->password_repeat;
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

    public function setPasswordRepeat($password_repeat)
    {
        $this->password_repeat = $password_repeat;
        return $this;
    }

    public function setRememberMe($remember_me)
    {
        $this->remember_me = $remember_me;
        return $this;
    }
    
    
    public function isValid()
    {
        return $this->getPassword() === $this->getPasswordRepeat();
    }




    
}
