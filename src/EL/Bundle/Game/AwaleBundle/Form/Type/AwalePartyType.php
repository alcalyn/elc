<?php

namespace EL\Bundle\Game\AwaleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AwalePartyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('seedsPerContainer', 'choice', array(
                'label'     => 'seeds.per.container',
                'choices'   => array(
                    3 => /** @ignore */ 3,
                    4 => /** @ignore */ 4,
                    5 => /** @ignore */ 5,
                    6 => /** @ignore */ 6,
                    7 => /** @ignore */ 7,
                ),
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\Bundle\Game\AwaleBundle\Entity\AwaleParty'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'el_awalebundle_awaleparty';
    }
}
