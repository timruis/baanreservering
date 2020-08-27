<?php

namespace App\Form;

use App\Entity\CourtReservation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ReservationType extends AbstractType
{
    private $user;
    private $ChosenDate;
    private $TwoHoursDate;
    /**
     * {@inheritdoc}
     */

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->ChosenDate =  (new \DateTime())->setTimestamp($options['ChosenDate']);
        $this->TwoHoursDate = $this->ChosenDate->add(new \DateInterval("PT2H"));
        $builder
            ->add('OtherPlayers', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'label'=>'Vul de spelers in:',
                'expanded' => false,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.id != :user')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->andWhere('cres.StartTime is NULL  or cres.StartTime not IN (:date, :date2)')
                        ->andWhere('crt.StartTime is NULL  or crt.StartTime not IN (:date, :date2)')
                        ->setParameter('user', $this->user->getId())
                        ->setParameter('date',$this->ChosenDate)
                        ->setParameter('date2', $this->TwoHoursDate )
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'attr'=>['class'=>'d-none'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('addedText', TextType::class, array(
                'mapped' => false,
                'required'=>false,
                'attr'=>['class'=>'d-none'],
                'label' => 'Eerste Speler: '.$this->user->getFirstname().' '.$this->user->getLastname() )
            ) // Trouble here!
            ->add('OtherPlayers0', EntityType::class, [
                'mapped' => false,
                'class' => User::class,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.CourtReservations', 'cres')
                        ->leftJoin('u.CourtReservationsTeam', 'crt')
                        ->andWhere('u.id != :user')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->andWhere('cres.StartTime is NULL  or cres.StartTime not IN (:date, :date2)')
                        ->andWhere('crt.StartTime is NULL  or crt.StartTime not IN (:date, :date2)')
                        ->setParameter('user', $this->user->getId())
                        ->setParameter('date',$this->ChosenDate)
                        ->setParameter('date2', $this->TwoHoursDate )
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'label'=>'Tweede speler',
                'required'=>true,
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
                        ->andWhere('u.id != :user')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->andWhere('cres.StartTime is NULL  or cres.StartTime not IN (:date, :date2)')
                        ->andWhere('crt.StartTime is NULL  or crt.StartTime not IN (:date, :date2)')
                        ->setParameter('user', $this->user->getId())
                        ->setParameter('date',$this->ChosenDate)
                        ->setParameter('date2', $this->TwoHoursDate )
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
                        ->andWhere('u.id != :user')
                        ->andWhere('u.Payed = true')
                        ->andWhere('u.ActivateUser = true')
                        ->andWhere('cres.StartTime is NULL  or cres.StartTime not IN (:date, :date2)')
                        ->andWhere('crt.StartTime is NULL  or crt.StartTime not IN (:date, :date2)')
                        ->setParameter('user', $this->user->getId())
                        ->setParameter('date',$this->ChosenDate)
                        ->setParameter('date2', $this->TwoHoursDate )
                        ->orderBy('u.Firstname, u.Lastname', 'ASC')
                        ;
                },
                'attr'=>['id'=>'combobox', 'class'=>'d-none'],
                'choice_label' => function (User $user) {
                    return  $user->getFirstname()." ".$user->getLastname() ;
                },])
            ->add('Save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'ChosenDate',
        ]);

        $resolver->setDefaults([
            'data_class' => CourtReservation::class,
        ]);
    }
}
