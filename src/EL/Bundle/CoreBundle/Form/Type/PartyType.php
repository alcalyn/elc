<?php

namespace EL\Bundle\CoreBundle\Form\Type;

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
            ->add('disallowObservers', 'checkbox', array(
                'label'     => 'disallow.observers',
                'required'  => false,
            ))
            ->add('disallowChat', 'checkbox', array(
                'label'     => 'disallow.chat',
                'required'  => false,
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\Core\Entity\Party',
        ));
    }

    public function getName()
    {
        return 'el_core_party_type';
    }
}
