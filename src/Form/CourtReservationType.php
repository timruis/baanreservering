<?php

namespace App\Form;

use App\Entity\CourtReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Court')
            ->add('Players')
            ->add('StartTime')
            ->add('playTimeAmount')
            ->add('Player')
            ->add('OtherPlayers')
            ->add('Save', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CourtReservation::class,
        ]);
    }
}
