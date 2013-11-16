<?php

namespace EL\ElCoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginType extends AbstractType
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
                ->add('remember_me', 'checkbox', array(
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
            'data_class' => 'EL\ELCoreBundle\Entity\Login',
        ));
    }

    public function getName()
    {
        return 'login';
    }
}