<?php

namespace EL\CoreBundle\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @Assert\Callback(methods={"isSamePasswords"})
 */
class Signup
{
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
    private $passwordRepeat;
    
    
    /**
     *
     * @var boolean 
     */
    private $rememberMe;
    
    
    
    
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
        return $this->passwordRepeat;
    }

    public function getRememberMe()
    {
        return $this->rememberMe;
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

    public function setPasswordRepeat($passwordRepeat)
    {
        $this->passwordRepeat = $passwordRepeat;
        return $this;
    }

    public function setRememberMe($rememberMe)
    {
        $this->rememberMe = $rememberMe;
        return $this;
    }
    
    
    public function isSamePasswords(ExecutionContext $context = null)
    {
        $same = $this->getPassword() === $this->getPasswordRepeat();
        
        if ((!$same) && (null !== $context)) {
            $context->addViolationAt('passwordRepeat', 'passwords.repeatition.fails', array(), null);
        }
        
        return $same;
    }
}
