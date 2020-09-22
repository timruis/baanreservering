<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
                'label'=>'Gebrukersnaam',
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
            ->add('Mobile', TelType::class,[
                'label'=>'Mobiel',
                'required'=>true,
                'attr'=> ['class'=>'login fadeIn third']
            ])
            ->add('Save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
