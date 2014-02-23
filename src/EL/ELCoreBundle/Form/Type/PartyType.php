<?php

namespace EL\ELCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label'     => 'party.title',
            ))
            ->add('private', 'checkbox', array(
                'label'     => 'private.only.invitation',
                'required'  => false,
            ))
            ->add('disallow_observers', 'checkbox', array(
                'label'     => 'disallow.observers',
                'required'  => false,
            ))
            ->add('disallow_chat', 'checkbox', array(
                'label'     => 'disallow.chat',
                'required'  => false,
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELCoreBundle\Entity\Party',
        ));
    }

    public function getName()
    {
        return 'el_core_party_type';
    }
}
