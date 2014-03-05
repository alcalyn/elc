<?php

namespace EL\ELCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('pseudo', 'text', array(
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('password', 'password', array(
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('passwordRepeat', 'password', array(
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('rememberMe', 'checkbox', array(
                    'label'     => 'Remember me on this computer',
                    'required'  => false,
                ))
                ->add('login', 'submit', array(
                    'attr'  => array('class' => 'btn btn-default'),
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELCoreBundle\Form\Entity\Signup',
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
