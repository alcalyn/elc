<?php

namespace EL\ELTicTacToeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SpecialPartyOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('option_1', 'text', array(
                    'label' => 'Option 1',
                ))
                ->add('option_tictactoe', 'text', array(
                    'label' => 'Option Tic Tac Toe',
                ))
                ->add('option_2', 'checkbox', array(
                    'label'     => 'Option 2',
                    'required'  => false,
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELTicTacToeBundle\Form\Entity\SpecialPartyOptions',
        ));
    }

    public function getName()
    {
        return 'special_party_options_type';
    }
}