<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordChangeType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'De wachtwoorden moeten matchen.',
                'options' => ['attr' => ['class'=>'login fadeIn thirth password-field',"pattern"=>"^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=\S+$).{6,}$","title"=> "Uw wachtwoord dient minimaal 8 karakters te hebben, minimaal 1 hoofdletter, minimaal 1 cijfer en minimaal 1 letter.","minlength"=>"8"]],
                'required' => true,
                'first_options'  => ['label' => 'Wachtwoord'],
                'second_options' => ['label' => 'Herhaal Wachtwoord'],
            ])
            ->add('Create', SubmitType::class, ['label' => 'Save'])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
