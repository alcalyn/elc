<?php

namespace EL\ElCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PartyOptionsType extends AbstractType
{
    
    private $game_service;
    
    public function __construct($game_service)
    {
        $this->game_service = $game_service;
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title', 'text', array(
                    'label'     => 'party.title',
                ))
                ->add('allow_observers', 'checkbox', array(
                    'label'     => 'allow.observers',
                    'required'  => false,
                ))
                ->add('private', 'checkbox', array(
                    'label'     => 'private.only.invitation',
                    'required'  => false,
                ))
                ->add('special_party_options', $this->game_service->getOptionsType())
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