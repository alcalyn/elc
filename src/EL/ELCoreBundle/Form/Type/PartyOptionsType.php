<?php

namespace EL\ElCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EL\ELCoreBundle\Services\PartyService;


class PartyOptionsType extends AbstractType
{
    
    private $party_service;
    
    
    public function __construct(PartyService $party_service)
    {
        $this->party_service = $party_service;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title', 'text', array(
                    'label'     => 'party.title',
                    'data'      => $this->party_service->generateRandomTitle(),
                ))
                ->add('allow_observers', 'checkbox', array(
                    'label'     => 'allow.observers',
                    'required'  => false,
                    'attr'      => array(
                        'checked'   => true,
                    ),
                ))
                ->add('private', 'checkbox', array(
                    'label'     => 'private.only.invitation',
                    'required'  => false,
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'EL\ELCoreBundle\Form\Entity\Login',
        ));
    }

    public function getName()
    {
        return 'party_options';
    }
}