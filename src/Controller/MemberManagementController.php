<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;
use App\Form\MemberType;
use App\Form\PasswordChangeType;
use App\Form\ProfileImageType;
use App\Form\RegisterType;
use App\Form\UserChangeType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Flex\Response;

class MemberManagementController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/admin/Change-Member-Info/{MemberId}", name="Member-Change")
     */
    public function ChangeMember($MemberId,EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $Member = $em->getRepository('App\Entity\User')->find($MemberId);


        $formPass = $this->createForm(PasswordChangeType::class);
        $formPass->handleRequest($request);
        if ($formPass->isSubmitted() && $formPass->isValid()) {
            $data=$formPass->getData();
            $checkPass = $passwordEncoder->encodePassword($Member, $data->getPassword());
            $Member->setPassword($checkPass);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('Member-Change', array('MemberId' => $MemberId));
        }

        $form = $this->createForm(UserChangeType::class, $Member);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $Member->setEmail($data->getEmail());
            $Member->setFirstname($data->getFirstname());
            $Member->setLastname($data->getLastname());
            $Member->setMobile($data->getMobile());
            $Member->setDescription($data->getDescription());
            $em->persist($Member);
            $em->flush();
            return $this->redirectToRoute('Member-Change', array('MemberId' => $MemberId));
        }

        $formProfImages = $this->createForm(ProfileImageType::class);
        $formProfImages->handleRequest($request);
        if ($formProfImages->isSubmitted() && $formProfImages->isValid()) {
            $data = $formProfImages->getData();
            $ProfileImage = $formProfImages->get('ProfileImage')->getData();
            if (isset($ProfileImage)) {
                $fileNameProfile = $this->generateUniqueFileName() . $ProfileImage->guessExtension();

                try {
                    $ProfileImage->move(
                        $this->getParameter('profile_directory'),
                        $fileNameProfile
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $Member->setProfileImage($fileNameProfile);
                $em->persist($Member);
                $em->flush();
            }
        }

        // See on what days the players play and how often
        $days= [
            0=>['day'=>'zondag','amount'=>0],
            1=>['day'=>'maandag','amount'=>0],
            2=>['day'=>'dinsdag','amount'=>0],
            3=>['day'=>'woensdag','amount'=>0],
            4=>['day'=>'donderdag','amount'=>0],
            5=>['day'=>'vrijdag','amount'=>0],
            6=>['day'=>'zaterdag','amount'=>0]
        ];
        foreach($Member->getCourtReservations() as $courtReservation) {
            $weekday=$courtReservation->getStartTime()->format('w');
            $days[$weekday]["amount"]++;
        }
        foreach($Member->getCourtReservationsTeam() as $courtReservation) {
            $weekday=$courtReservation->getStartTime()->format('w');
            $days[$weekday]["amount"]++;
        }

        // See with whom the member has played with and how much
        $Players= [];
        foreach($Member->getCourtReservations() as $courtReservation) {
            foreach($courtReservation->getOtherPlayers() as $Player) {
                if (!isset($Players[$Player->getId()]) && empty($Players[$Player->getId()])) {
                    $Players[$Player->getId()]["amount"] = 0;
                    $Players[$Player->getId()]["Name"] = $Player->getFirstname() . " " . $Player->getLastname();
                }
                $Players[$Player->getId()]["amount"] ++;
            }
            if ($courtReservation->getReservationType()==6) {
                if (!isset($Players[ucwords($courtReservation->getMemoText())]) && empty($Players[ucwords($courtReservation->getMemoText())])) {
                    $Players[ucwords($courtReservation->getMemoText())]["amount"] = 0;
                    $Players[ucwords($courtReservation->getMemoText())]["Name"] = ucwords($courtReservation->getMemoText())." (introduce)";
                }
                $Players[ucwords($courtReservation->getMemoText())]["amount"] ++;
            }
        }
        foreach($Member->getCourtReservationsTeam() as $courtReservation) {
             $Player = $courtReservation->getPlayer();
            if (!isset($Players[$Player->getId()]) && empty($Players[$Player->getId()])) {
                $Players[$Player->getId()]["amount"] = 0;
                $Players[$Player->getId()]["Name"] = $Player->getFirstname() . " " . $Player->getLastname();
            }
            $Players[$Player->getId()]["amount"] ++;
        }
        arsort($Players);

        // The court that the member play on, so you can see what the player likes
        $Courts= [];
        foreach($Member->getCourtReservations() as $courtReservation) {
            if (!isset($Courts[$courtReservation->getCourt()]) && empty($Courts[$courtReservation->getCourt()])) {
                $Courts[$courtReservation->getCourt()]["amount"] = 0;
                $Courts[$courtReservation->getCourt()]["Court"] = $courtReservation->getCourt();
            }
            $Courts[$courtReservation->getCourt()]["amount"] ++;
        }
        foreach($Member->getCourtReservationsTeam() as $courtReservation) {
            if (!isset($Courts[$courtReservation->getCourt()]) && empty($Courts[$courtReservation->getCourt()])) {
                $Courts[$courtReservation->getCourt()]["amount"] = 0;
                $Courts[$courtReservation->getCourt()]["Court"] = $courtReservation->getCourt();
            }
            $Courts[$courtReservation->getCourt()]["amount"] ++;
        }
        ksort($Courts);

        // Speeltype reserveringen
        $TYPEs= [];
        foreach($Member->getCourtReservations() as $courtReservation) {
            if (!isset($TYPEs[$courtReservation->getReservationType()]) && empty($TYPEs[$courtReservation->getReservationType()])) {
                $TYPEs[$courtReservation->getReservationType()]["amount"] = 0;
                $TYPEs[$courtReservation->getReservationType()]["Court"] = $courtReservation->getReservationType();
            }
            $TYPEs[$courtReservation->getReservationType()]["amount"] ++;
        }
        foreach($Member->getCourtReservationsTeam() as $courtReservation) {
            if (!isset($TYPEs[$courtReservation->getReservationType()]) && empty($TYPEs[$courtReservation->getReservationType()])) {
                $TYPEs[$courtReservation->getReservationType()]["amount"] = 0;
                $TYPEs[$courtReservation->getReservationType()]["Court"] = $courtReservation->getReservationType();
            }
            $TYPEs[$courtReservation->getReservationType()]["amount"] ++;
        }
        ksort($TYPEs);


        //makes a graph of for reservations on every month
        $Reservations= [];
        foreach($Member->getCourtReservations() as $courtReservation) {
            if (!isset($Reservations[$courtReservation->getStartTime()->format('Ymm')]) && empty($Reservations[$courtReservation->getStartTime()->format('Ymm')])) {
                $Reservations[$courtReservation->getStartTime()->format('Ymm')]["amount"] = 0;
                $Reservations[$courtReservation->getStartTime()->format('Ymm')]["Date"] = new \DateTime("01-".$courtReservation->getStartTime()->format('m-Y'));
            }
            $Reservations[$courtReservation->getStartTime()->format('Ymm')]["amount"] ++;
        }
        foreach($Member->getCourtReservationsTeam() as $courtReservation) {
            if (!isset($Reservations[$courtReservation->getStartTime()->format('Ymm')]) && empty($Reservations[$courtReservation->getStartTime()->format('Ymm')])) {
                $Reservations[$courtReservation->getStartTime()->format('Ymm')]["amount"] = 0;
                $Reservations[$courtReservation->getStartTime()->format('Ymm')]["Date"] = new \DateTime("01-".$courtReservation->getStartTime()->format('m-Y'));
            }
            $Reservations[$courtReservation->getStartTime()->format('Ymm')]["amount"] ++;
        }
        ksort($Reservations);

        return $this->render('Member/MemberRegistry.html.twig', [
            'Member' => $form->createView(),
            'Password' => $formPass->createView(),
            'AccountData' => $Member,
            'days'=>$days,
            'Reservations'=>$Reservations,
            'Courts'=>$Courts,
            'types'=>$TYPEs,
            'Players'=>$Players,
            'ImagesProf' => $formProfImages->createView(),
            'Title'=> "Change Existing Member"
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }


    /**
     * @Route("/Register-admin", name="new-member-Registry")
     */
    public function Register(EntityManagerInterface $em, Request $request,AuthenticationUtils $authenticationUtils): \Symfony\Component\HttpFoundation\Response
    {

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $User = new User();
            $User->setUsername($data->getUsername());
            $User->setEmail($data->getEmail());
            $checkPass = $this->encoder->encodePassword($User, $data->getPassword());
            $User->setPassword($checkPass);
            $User->setFirstname($data->getFirstname());
            $User->setLastname($data->getLastname());
            $User->setActivateUser(false);
            $User->setPayed(false);
            $User->setWinterMember(false);
            $User->setSummerMember(false);
            $em->persist($User);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'controller_name' => 'CourtReservationController',
            'UserReg'=>$form->createView(),
            'imgnumber' => rand(1, 3)
        ]);
    }

    /**
     * @Route("/admin/Delete-Member-Info/{MemberId}", name="Member-Delete")
     */
    public function DeleteMember($MemberId,EntityManagerInterface $em, Request $request)
    {
        $Member = $em->getRepository('App\Entity\User')->find($MemberId);
        foreach ($Member->getPageVisits() as $pageVisit){
            $em->remove($pageVisit);
            $em->flush();
        }
        foreach ($Member->getCourtReservations() as $CourtReservation){
            $em->remove($CourtReservation);
            $em->flush();
        }
        $em->remove($Member);
        $em->flush();
        return $this->redirectToRoute('Members');
    }

    /**
     * @Route("/admin/Members", name="Members")
     */
    public function ListedMember()
    {
        $Member = $this->getDoctrine()->getRepository(User::class)->findMembersByOrder();

        return $this->render('Member/ShowAllMember.html.twig', [
            'Members' => $Member,
            'Title'=> "Members"
        ]);


    }

    /**
     * @Route("/admin/check/payment", name="Payment")
     */
    public function changepayed(EntityManagerInterface $em,Request $request)
    {

        $id = $request->get('id');
        $User = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($User->getPayed()){
            $User->setPayed(false);
        }else {
            $User->setPayed(true);
        }
        $em->persist($User);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($User->getPayed())
        );
    }
    /**
     * @Route("/admin/check/active", name="Activate")
     */
    public function changeActive(EntityManagerInterface $em,Request $request)
    {

        $id = $request->get('id');
        $User = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($User->getActivateUser()){
            $User->setActivateUser(false);
        }else {
            $User->setActivateUser(true);
        }
        $em->persist($User);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($User->getActivateUser())
        );
    }
    /**
     * @Route("/admin/check/Wintermembership", name="WinterMember")
     */
    public function changeWinterMember(EntityManagerInterface $em,Request $request)
    {

        $id = $request->get('id');
        $User = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($User->getWinterMember()){
            $User->setWinterMember(false);
        }else {
            $User->setWinterMember(true);
        }
        $em->persist($User);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($User->getWinterMember())
        );
    }

    /**
     * @Route("/admin/check/Summermembership", name="SummerMember")
     */
    public function changeSummerMember(EntityManagerInterface $em,Request $request)
    {

        $id = $request->get('id');
        $User = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($User->getSummerMember()){
            $User->setSummerMember(false);
        }else {
            $User->setSummerMember(true);
        }
        $em->persist($User);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($User->getSummerMember())
        );
    }

    /**
     * @Route("/priviliged/Members/list", name="Member_List_data")
     */
    public function MemberListData()
    {
        $Members = $this->getDoctrine()->getRepository(User::class)->findAll();

        $allMember = array();
        $i=1;
        foreach ($Members as $Member) {
            $urlChange = $this->generateUrl(
                'Member-Change',
                [
                    'MemberId' => $Member->getId(),
                    'tabNR' => '1'
                ]
            );
            $urlDelete = $this->generateUrl(
                'Member-Delete',
                ['MemberId' => $Member->getId()]
            );
            $profile=$Member->getProfileImage();
            if(isset($profile) && !empty($profile != NULL )){
                $image='<img alt = "Image" style="height:95px;object-fit: cover" id="image'.$i.'" data-canvasId="canvas'.$i.'" data-id="image'.$i.'" data-src="/uploads/images/profile/'.$Member->getProfileImage() .'" src="/uploads/images/profile/'.$Member->getProfileImage() .'" class="member avatar avatar-lg mt-1" >';
                }else {
                $image='geen foto toegevoegd ';
                }
            if( $Member->getpayed() == 1 ){
                $payed="checked";
                $payedYN="Ja";
            }else{
                $payed=" ";
                $payedYN="Nee";
            }
            if( $Member->getActivateUser() == 1 ){
                $ActivateUser="checked";
                $ActivateUserYN="Ja";
                $ActivateClass="IsActive";
            }else{
                $ActivateUser=" ";
                $ActivateUserYN="Nee";
                $ActivateClass="IsNotActive";
            }
            if( $Member->getWinterMember() == 1 ){
                $WinterMember="checked";
                $WinterMemberYN="Ja";
            }else{
                $WinterMember=" ";
                $WinterMemberYN="Nee";
            }
            if( $Member->getSummerMember() == 1 ){
                $SummerMember="checked";
                $SummerMemberYN="Ja";
            }else{
                $SummerMember=" ";
                $SummerMemberYN="Nee";
            }

            array_push($allMember, array(
                $i,
                $image,
                $Member->getFirstname(),
                $Member->getLastname(),
                $Member->getEmail(),
                $Member->getMobile(),
                '<p><label for="payed">
                    <input class="payed filled-in" type="checkbox" style="z-index:100;height:100px;width:100px;opacity: 0;pointer-events: all;" '.$payed.'  name="payed" value="'.$Member->getId().'"/>
                        <span>Heeft betaald</span>
                    </label>
                    <span id="haspayed'.$Member->getId().'">'.$payedYN.'</span>
                </p>',
                '<p class="'.$ActivateClass.'"><label for="Active">
                    <input class="Active filled-in " type="checkbox" style="z-index:100;height:100px;width:100px;opacity: 0;pointer-events: all;" '.$ActivateUser.'  name="Active" value="'.$Member->getId().'"/>
                        <span>Is actief</span>
                    </label>
                    <span id="hasActived'.$Member->getId().'">'.$ActivateUserYN.'</span>
                </p>',
                '<p><label for="Winter">
                    <input class="Winter filled-in" type="checkbox" style="z-index:100;height:100px;width:100px;opacity: 0;pointer-events: all;" '.$WinterMember.'  name="Winter" value="'.$Member->getId().'"/>
                        <span>Is Winterlid</span>
                    </label>
                    <span id="hasWinterMember'.$Member->getId().'">'.$WinterMemberYN.'</span>
                </p>',
                '<p><label for="Summer">
                    <input class="Summer filled-in" type="checkbox" style="z-index:100;height:100px;width:100px;opacity: 0;pointer-events: all;" '.$SummerMember.'  name="Summer" value="'.$Member->getId().'"/>
                        <span>Is Zomerlid</span>
                    </label>
                    <span id="hasSummerMember'.$Member->getId().'">'.$SummerMemberYN.'</span>
                </p>',
                '<a class="btn btn-outline-primary" href="' . $urlChange . '">Change <span class="fas fa-pen-square"></span></a>',
                ' <button type="button" id="' .$Member->getId() . '" class="Delete btn btn-danger" onclick="ModalDelete(\'' . $urlDelete . '\')" data-toggle="modal" data-target="#DeletePlayer">Delete <span class="fas fa-times-circle"></span></button>'
            ));
            $i++;
        }
        $GoodArray = array('data' => $allMember);
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($GoodArray)
        );
    }

}
