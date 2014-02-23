<?php

namespace EL\ELTicTacToeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EL\ELTicTacToeBundle\Entity\Party;

class TicTacToePartyOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numberOfParties', 'choice', array(
                'label'     => 'number.of.parties',
                'choices'   => array(1, 2, 3, 4, 5),
            ))
            ->add('victoryCondition', 'choice', array(
                'label'     => 'victorycondition',
                'choices'   => array(
                    Party::END_ON_PARTIES_NUMBER    => 'parties',
                    Party::END_ON_WINS_NUMBER       => 'wins',
                    Party::END_ON_DRAWS_NUMBER      => 'draws',
                ),
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EL\ELTicTacToeBundle\Entity\Party',
        ));
    }

    public function getName()
    {
        return 'tictactoe_options_type';
    }
}
