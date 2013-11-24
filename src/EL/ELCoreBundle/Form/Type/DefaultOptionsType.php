<?php

namespace EL\ElCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DefaultOptionsType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('option_1', 'text', array(
                    'label' => 'Option 1',
                ))
                ->add('option_2', 'checkbox', array(
                    'label'     => 'Option 2',
                    'required'  => false,
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELCoreBundle\Form\Entity\DefaultOptions',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'default_options';
    }
}