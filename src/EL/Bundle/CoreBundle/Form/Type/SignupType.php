<?php

namespace EL\Bundle\CoreBundle\Form\Type;

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
                    'label'     => 'remember.me',
                    'required'  => false,
                ))
                ->add('createAccount', 'submit', array(
                    'label' => 'create.account',
                ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\Bundle\CoreBundle\Form\Entity\Signup',
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
