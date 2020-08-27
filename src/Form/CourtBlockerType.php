<?php

namespace App\Form;

use App\Entity\CourtReservation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtBlockerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ChooseCourt', ChoiceType::class, [
                'label' => 'kies de baan',
                'multiple' => true,
                'mapped' => false,
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                ]
            ])
            ->add('Steps', ChoiceType::class, [
                'label' => 'kies de herhalende tijd stappen',
                'mapped' => false,
                'choices' => [
                    'Geen' => 0,
                    'Dagelijks' => 1,
                    'Wekelijks' => 2,
                    'Maandelijks' => 3
                ]
            ])
            ->add('StartTime', DateType::class, [
                'widget' => 'single_text',
                'label'=>  'Start Datum',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('EndDate', DateType::class, [
                'widget' => 'single_text',
                'label'=>  'Eind Datum',
                'mapped' => false,
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('startingTime', TimeType::class, [
                'widget' => 'choice',
                'mapped' => false,
                'label'=>  'start tijd',
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute',
                ],
                'minutes'=>[
                    "00"=>"00",
                    "30"=> "30",
                ],
                'hours'=>[
                    "8"=>"8",
                    "9"=>"9",
                    "10"=>"10",
                    "11"=>"11",
                    "12"=>"12",
                    "13"=>"13",
                    "14"=>"14",
                    "15"=>"15",
                    "16"=>"16",
                    "17"=>"17",
                    "18"=>"18",
                    "19"=>"19",
                    "20"=>"20",
                    "21"=>"21",
                    "22"=>"22",
                    "23"=>"23",
                ],
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'timepickerStart'],
            ])
            ->add('endingTime',TimeType::class, [
                'widget' => 'choice',
                'mapped' => false,
                'label'=>  'eind tijd',
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute',
                ],
                'minutes'=>[
                    "00"=>"00",
                    "30"=> "30"
                ],
                'hours'=>[
                    "8"=>"8",
                    "9"=>"9",
                    "10"=>"10",
                    "11"=>"11",
                    "12"=>"12",
                    "13"=>"13",
                    "14"=>"14",
                    "15"=>"15",
                    "16"=>"16",
                    "17"=>"17",
                    "18"=>"18",
                    "19"=>"19",
                    "20"=>"20",
                    "21"=>"21",
                    "22"=>"22",
                    "23"=>"23",
                ],
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'timepickerEnd'],
            ])
            ->add('ReservationType', ChoiceType::class, [
                'label' => 'Soort reservation',
                'multiple' => false,
                'choices' => [
                    'Les' => 1,
                    'Toss' => 2,
                    'Competitie' => 3,
                    'Event' => 4,
                    'Verhuur' => 5,
                    'Tennis Kamp' => 6,
                    'Overig' => 7,
                ]
            ])
            ->add('MemoText')
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
