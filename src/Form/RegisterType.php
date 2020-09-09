<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username',TextType::class,[
                'label'=>'Gebruikersnaam',
                'required'=>true,
                'attr'=> ['class'=>'login fadeIn second']
            ])
            ->add('Firstname',TextType::class,[
                'label'=>'Voornaam',
                'required'=>true,
                'attr'=> ['class'=>'login fadeIn second']
            ])
            ->add('Lastname',TextType::class,[
                'label'=>'Achternaam',
                'required'=>true,
                'attr'=> ['class'=>'login fadeIn second']
            ])
            ->add('email',EmailType::class,[
                'label'=>'Email',
                'label_attr'=>['style'=>'display:block;'],
                'required'=>true,
                'attr'=> ['class'=>'login fadeIn thirth']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'De wachtwoorden moeten matchen.',
                'options' => ['attr' => ['class'=>'login fadeIn thirth password-field',"pattern"=>"^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=\S+$).{6,}$","title"=> "Uw wachtwoord dient minimaal 8 karakters te hebben, minimaal 1 hoofdletter, minimaal 1 cijfer en minimaal 1 letter.","minlength"=>"8"]],
                'required' => true,
                'first_options'  => ['label' => 'Wachtwoord'],
                'second_options' => ['label' => 'Herhaal Wachtwoord'],
            ])
            ->add('maak_account', SubmitType::class, ['label' => 'registreer mij']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
