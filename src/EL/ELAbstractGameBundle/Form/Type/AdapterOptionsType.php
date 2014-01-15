<?php

namespace EL\ELAbstractGameBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdapterOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELAbstractGameBundle\Form\Entity\AdapterOptions',
        ));
    }

    public function getName()
    {
        return 'adapter_options_type';
    }
}