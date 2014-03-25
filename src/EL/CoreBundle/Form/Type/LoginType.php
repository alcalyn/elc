<?php

namespace EL\CoreBundle\Form\Type;

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
                    'label'     => 'remember.me',
                    'required'  => false,
                ))
                ->add('login', 'submit')
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\CoreBundle\Form\Entity\Login',
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
