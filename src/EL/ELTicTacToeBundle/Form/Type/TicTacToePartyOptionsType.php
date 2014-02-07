<?php

namespace EL\ELTicTacToeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TicTacToePartyOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('first_player', 'choice', array(
                    'label'     => 'first.player',
                    'choices'   => array(
                        0 => 'random',
                        1 => 'player.1',
                        2 => 'player.2',
                    ),
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELTicTacToeBundle\Form\Entity\TicTacToePartyOptions',
        ));
    }

    public function getName()
    {
        return 'tictactoe_options_type';
    }
}
