<?php

namespace EL\ELCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EL\ELCoreBundle\Form\Type\PartyType as CorePartyType;

class OptionsType extends AbstractType
{
    /**
     * @var AbstractType
     */
    private $extendedPartyType;
    
    
    public function __construct(AbstractType $extendedPartyType)
    {
        $this->extendedPartyType = $extendedPartyType;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('coreParty', new CorePartyType())
                ->add('extendedOptions', $this->extendedPartyType)
                ->add('createGame', 'submit')
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELCoreBundle\Form\Entity\Options',
        ));
    }
    
    public function getName()
    {
        return 'el_core_options_type';
    }
}
