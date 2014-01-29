<?php

namespace EL\ELCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PartyOptionsType extends AbstractType
{
    
    private $special_party_options;
    
    public function __construct($special_party_options)
    {
        $this->special_party_options = $special_party_options;
    }
    
    
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
                ->add('special_party_options', $this->special_party_options)
                ->add('create.game', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELCoreBundle\Form\Entity\PartyOptions',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'party_options';
    }
}