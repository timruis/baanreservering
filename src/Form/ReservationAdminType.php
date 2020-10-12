<?php

namespace App\Form;

use App\Entity\CourtReservation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ReservationAdminType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('OtherPlayers', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'required'=> false,
                'label'=>'Vul de spelers in:',
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'attr'=>['class'=>'d-none'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('Player', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'label'=>'reserverende speler',
                'required'=>false,
                'attr'=>['id'=>'combobox', 'class'=>'d-none'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('OtherPlayers0', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'label'=>'Tweede speler',
                'required'=>false,
                'attr'=>['id'=>'combobox', 'class'=>'d-none'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('OtherPlayers1', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'label'=>'Derde speler',
                'required'=>false,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'attr'=>['id'=>'combobox', 'class'=>'d-none'],
                'choice_attr' =>            function (User $user) {
                        if ($this->user->getId() == $user->getId()) {
                            return ['class'=>'dipslay:none;'];
                        }else{
                            return ['style'=>'dipslay:block;'];
                        }
                    },

                'choice_label' => function (User $user) {
                    return $user->getFirstname() . " " . $user->getLastname();
                }
                ,])
            ->add('OtherPlayers2', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'label'=>'Vierde speler',
                'required'=>false,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'attr'=>['id'=>'combobox', 'class'=>'d-none'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('ReservationType', CheckboxType::class, [
                'label'    => 'Is dit baan verhuur?',
                'required' => false,
                'attr'=>['id'=>'verhuur'],
            ])
            ->add('MemoText',TextareaType::class,[
                'label'    => 'Wie spelen er? (Vul ook minimaal 1 telefoon nummer in)',
                'required' => false,
                'label_attr'=>['class'=>'verhuurLabel d-none'],
                'attr'=>['id'=>'verhuurBox', 'class'=>'d-none']
                ])
            ->add('Players',NumberType::class,[
                'label'    => 'Hoeveel spelen er?',
                'required' => false,
                'label_attr'=>['class'=>'verhuurLabel d-none'],
                'attr'=>['id'=>'verhuurNumber', 'class'=>'d-none']
            ])
            ->add('introduce', CheckboxType::class, [
                'mapped'=> false,
                'label'    => 'Is dit een introduce?(maximaal 2 te introduceren)',
                'required' => false,
                'attr'=>['id'=>'introduce'],
            ])
            ->add('MemoTextIntroduce',TextType::class,[
                'mapped'=> false,
                'label'    => 'Wie wordt er geintroduceerd?',
                'required' => false,
                'label_attr'=>['class'=>'IntroduceLabel d-none'],
                'attr'=>['id'=>'introduceBox', 'class'=>'d-none']
            ])
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
