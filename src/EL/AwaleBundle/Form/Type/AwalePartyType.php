<?php

namespace EL\AwaleBundle\Form\Type;

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
                    3 => /** @Ignore */ 3,
                    4 => /** @Ignore */ 4,
                    5 => /** @Ignore */ 5,
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
            'data_class' => 'EL\AwaleBundle\Entity\AwaleParty'
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
