<?php

namespace EL\ELCoreBundle\Form\Type;

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
                ->add('rememberMe', 'checkbox', array(
                    'label'     => 'Remember me on this computer',
                    'required'  => false,
                ))
                ->add('login', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELCoreBundle\Form\Entity\Login',
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
