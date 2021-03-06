<?php

namespace EL\CheckersBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class CheckersOptionsType extends AbstractType
{
    /**
     * @var Translator
     */
    private $t;
    
    
    public function __construct(Translator $translator)
    {
        $this->t = $translator;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('boardSize', 'integer', array(
                'label' => 'board.size',
                'attr' => array(
                    'placeholder'   => '8, 10, 12',
                    'min'           => 4,
                    'max'           => 14,
                    'step'          => 2,
                ),
            ))
            ->add('squareUsed', 'checkbox', array(
                'label' => 'square.used',
                'required'  => false,
                'attr'      => array(
                    'class'         => 'cb-restyle cb-blackwhite',
                    'data-cb-false'  => $this->t->trans('white.es'),
                    'data-cb-true' => $this->t->trans('black.es'),
                ),
            ))
            ->add('rightSquare', 'checkbox', array(
                'label' => 'right.square',
                'required'  => false,
                'attr'      => array(
                    'class'         => 'cb-restyle cb-blackwhite',
                    'data-cb-false'  => $this->t->trans('white.e'),
                    'data-cb-true' => $this->t->trans('black.e'),
                ),
            ))
            ->add('backwardCapture', 'checkbox', array(
                'label' => 'backward.capture',
                'required' => false,
            ))
            ->add('longRangeKing', 'checkbox', array(
                'label' => 'long.range.king',
                'required' => false,
            ))
            ->add('kingStopsBehind', 'checkbox', array(
                'label' => 'king.stops.behind',
                'required' => false,
            ))
            ->add('menJumpKing', 'checkbox', array(
                'label' => 'men.jump.king',
                'required' => false,
            ))
            ->add('kingPassing', 'checkbox', array(
                'label' => 'king.passing',
                'required' => false,
            ))
            ->add('blowUp', 'checkbox', array(
                'label' => 'blow.up',
                'required' => false,
            ))
            ->add('forceCapture', 'checkbox', array(
                'label' => 'force.capture',
                'required' => false,
            ))
            ->add('forceCaptureQuantity', 'checkbox', array(
                'label' => 'force.capture.quantity',
                'required' => false,
            ))
            ->add('forceCaptureQuality', 'checkbox', array(
                'label' => 'force.capture.quality',
                'required' => false,
            ))
            ->add('forceCaptureKingOrder', 'checkbox', array(
                'label' => 'force.capture.kingorder',
                'required' => false,
            ))
            ->add('forceCapturePreference', 'checkbox', array(
                'label' => 'force.capture.preference',
                'required' => false,
            ))
            ->add('firstPlayer', 'checkbox', array(
                'label' => 'first.player',
                'required'  => false,
                'attr'      => array(
                    'class'         => 'cb-restyle cb-blackwhite',
                    'data-cb-false'  => $this->t->trans('white.s'),
                    'data-cb-true' => $this->t->trans('black.s'),
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
            'data_class' => 'EL\CheckersBundle\Checkers\Variant'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'el_checkersbundle_checkersparty';
    }
}
