<?php

namespace App\Form;

use App\Entity\CourtReservation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('OtherPlayers', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'label'=>'Vul de spelers in',
                'expanded' => false,
                'attr'=>['style'=>'display:none;'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('OtherPlayers0', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC');
                },
                'label'=>'Eerste speler',
                'required'=>false,
                'attr'=>['id'=>'combobox'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('OtherPlayers1', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'label'=>'Tweede speler',
                'required'=>false,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC');
                },
                'attr'=>['id'=>'combobox'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('OtherPlayers2', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'label'=>'Derde speler',
                'required'=>false,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC');
                },
                'attr'=>['id'=>'combobox'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
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
