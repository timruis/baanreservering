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

class UserChangeType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles and Rights',
                'mapped' => true,
                'multiple' => true,
                'preferred_choices' => [
                    'Roles' =>[
                        'Admin' => 'ROLE_ADMIN',
                        'Super user' => 'ROLE_SUPER-USER',
                        'User' => 'ROLE_USER'
                    ]
                ],
                'choices'=>['Roles' =>[
                    'Admin' => 'ROLE_ADMIN',
                    'Super user' => 'ROLE_SUPER-USER',
                    'User' => 'ROLE_USER'
                ],
                'Rights' =>[
                    'Baan Beheer' => 'ROLE_COURT',
                    'Gerbuikers' => 'ROLE_USERMANAGEMENT',
                    'Training' => 'ROLE_Training',
                ]
                ]
            ])
            ->add('Create', SubmitType::class, ['label' => 'Save / add new'])
            ->add('StayOn', SubmitType::class, ['label' => 'Save / keep model'])
            ->add('OpenList', SubmitType::class, ['label' => 'Save / open list']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
