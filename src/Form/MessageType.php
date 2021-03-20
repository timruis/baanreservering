<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startingDate', DateType::class, [
                'required'   => true,
                'html5' => true,
            ])
            ->add('endingDate', DateType::class, [
                'required'   => true,
                'html5' => true,
            ])
            ->add('message')
            ->add('color', ChoiceType::class, [
                'label' => 'give the message a color',
                'mapped' => true,
                'multiple' => false,
                'required'   => true,
                'choices'=>[
                    'primary' => 'primary',
                    'secondary' => 'secondary',
                    'success' => 'success',
                    'danger' => 'danger',
                    'warning' => 'warning',
                    'info' => 'info',
                    'light' => 'light',
                    'dark' => 'dark text-light bg-dark',
                    'muted' => 'muted',
                    'white' => 'white text-white bg-dark',
                ]
            ])
            ->add('Create', SubmitType::class, ['label' => 'Save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
