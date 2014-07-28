<?php

namespace EL\TicTacToeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EL\TicTacToeBundle\Entity\TicTacToeParty;

class TicTacToePartyOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numberOfParties', 'choice', array(
                'label'     => 'number.of.parties',
                'choices'   => array(
                    1 => /** @ignore */ 1,
                    2 => /** @ignore */ 2,
                    3 => /** @ignore */ 3,
                    4 => /** @ignore */ 4,
                    5 => /** @ignore */ 5,
                ),
            ))
            ->add('victoryCondition', 'choice', array(
                'label'     => 'victorycondition',
                'choices'   => array(
                    TicTacToeParty::END_ON_PARTIES_NUMBER    => 'parties',
                    TicTacToeParty::END_ON_WINS_NUMBER       => 'wins',
                    TicTacToeParty::END_ON_DRAWS_NUMBER      => 'draws',
                ),
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\TicTacToeBundle\Entity\TicTacToeParty',
        ));
    }

    public function getName()
    {
        return 'tictactoe_options_type';
    }
}
