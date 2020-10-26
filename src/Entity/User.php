<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 *@ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_2DA17977F85E0677", columns={"username"})})
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\File(mimeTypes={ "image/jpeg","image/jpg","image/png" })
     */
    private $ProfileImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\File(mimeTypes={ "image/jpeg" ,"image/jpg","image/png" })
     */
    private $BackgroundImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Lastname;

    /**
     * @var string
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Mobile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $CompanyRole;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CourtReservation", mappedBy="Player")
     */
    private $CourtReservations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CourtReservation", mappedBy="OtherPlayers")
     */
    private $CourtReservationsTeam;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ActivateUser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Training", mappedBy="Teacher")
     */
    private $trainings;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Payed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $SummerMember;

    /**
     * @ORM\Column(type="boolean")
     */
    private $WinterMember;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PageVisit", mappedBy="User")
     */
    private $pageVisits;

    /**
     * @ORM\OneToMany(targetEntity=ForgetPasswordCode::class, mappedBy="User")
     */
    private $forgetPasswordCodes;

    public function __construct()
    {
        $this->CourtReservations = new ArrayCollection();
        $this->CourtReservationsTeam = new ArrayCollection();
        $this->trainings = new ArrayCollection();
        $this->PageVisits = new ArrayCollection();
        $this->forgetPasswordCodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt()
    {
        // leaving blank - I don't need/have a password!
        return null;
    }
    public function eraseCredentials()
    {
        // leaving blank - I don't need/have a password!
    }
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getProfileImage(): ?string
    {
        return $this->ProfileImage;
    }

    public function setProfileImage(?string $ProfileImage): self
    {
        $this->ProfileImage = $ProfileImage;

        return $this;
    }

    public function getBackgroundImage(): ?string
    {
        return $this->BackgroundImage;
    }

    public function setBackgroundImage(?string $BackgroundImage): self
    {
        $this->BackgroundImage = $BackgroundImage;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->Firstname;
    }

    public function setFirstname(string $Firstname): self
    {
        $this->Firstname = $Firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->Lastname;
    }

    public function setLastname(string $Lastname): self
    {
        $this->Lastname = $Lastname;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->Mobile;
    }

    public function setMobile(?string $Mobile): self
    {
        $this->Mobile = $Mobile;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCompanyRole(): ?string
    {
        return $this->CompanyRole;
    }

    public function setCompanyRole(?string $CompanyRole): self
    {
        $this->CompanyRole = $CompanyRole;

        return $this;
    }

    /**
     * @return Collection|CourtReservation[]
     */
    public function getCourtReservations(): Collection
    {
        return $this->CourtReservations;
    }

    public function addCourtReservations(CourtReservation $CourtReservations): self
    {
        if (!$this->CourtReservations->contains($CourtReservations)) {
            $this->CourtReservations[] = $CourtReservations;
            $CourtReservations->setCourtReservations($this);
        }

        return $this;
    }

    public function removeCourtReservations(CourtReservation $CourtReservation): self
    {
        if ($this->CourtReservations->contains($CourtReservation)) {
            $this->CourtReservations->removeElement($CourtReservation);
            // set the owning side to null (unless already changed)
            if ($CourtReservation->getPlayer() === $this) {
                $CourtReservation->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CourtReservation[]
     */
    public function getCourtReservationsTeam(): Collection
    {
        return $this->CourtReservationsTeam;
    }

    public function addCourtReservationsTeam(CourtReservation $CourtReservationsTeam): self
    {
        if (!$this->CourtReservationsTeam->contains($CourtReservationsTeam)) {
            $this->CourtReservationsTeam[] = $CourtReservationsTeam;
            $CourtReservationsTeam->addOtherPlayer($this);
        }

        return $this;
    }

    public function removeCourtReservationsTeam(CourtReservation $CourtReservationsTeam): self
    {
        if ($this->CourtReservationsTeam->contains($CourtReservationsTeam)) {
            $this->CourtReservationsTeam->removeElement($CourtReservationsTeam);
            $CourtReservationsTeam->removeOtherPlayer($this);
        }

        return $this;
    }

    public function getActivateUser(): ?bool
    {
        return $this->ActivateUser;
    }

    public function setActivateUser(bool $ActivateUser): self
    {
        $this->ActivateUser = $ActivateUser;

        return $this;
    }

    /**
     * @return Collection|Training[]
     */
    public function getTrainings(): Collection
    {
        return $this->trainings;
    }

    public function addTraining(Training $training): self
    {
        if (!$this->trainings->contains($training)) {
            $this->trainings[] = $training;
            $training->setTeacher($this);
        }

        return $this;
    }

    public function removeTraining(Training $training): self
    {
        if ($this->trainings->contains($training)) {
            $this->trainings->removeElement($training);
            // set the owning side to null (unless already changed)
            if ($training->getTeacher() === $this) {
                $training->setTeacher(null);
            }
        }

        return $this;
    }

    public function getPayed(): ?bool
    {
        return $this->Payed;
    }

    public function setPayed(bool $Payed): self
    {
        $this->Payed = $Payed;

        return $this;
    }

    public function getSummerMember(): ?bool
    {
        return $this->SummerMember;
    }

    public function setSummerMember(bool $SummerMember): self
    {
        $this->SummerMember = $SummerMember;

        return $this;
    }

    public function getWinterMember(): ?bool
    {
        return $this->WinterMember;
    }

    public function setWinterMember(bool $WinterMember): self
    {
        $this->WinterMember = $WinterMember;

        return $this;
    }
    /**
     * @return Collection|PageVisit[]
     */
    public function getPageVisits(): Collection
    {
        return $this->pageVisits;
    }

    public function addPageVisit(PageVisit $pageVisit): self
    {
        if (!$this->pageVisits->contains($pageVisit)) {
            $this->pageVisits[] = $pageVisit;
            $pageVisit->setUser($this);
        }

        return $this;
    }

    public function removePageVisit(PageVisit $pageVisit): self
    {
        if ($this->pageVisits->contains($pageVisit)) {
            $this->pageVisits->removeElement($pageVisit);
            // set the owning side to null (unless already changed)
            if ($pageVisit->getUser() === $this) {
                $pageVisit->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ForgetPasswordCode[]
     */
    public function getForgetPasswordCodes(): Collection
    {
        return $this->forgetPasswordCodes;
    }

    public function addForgetPasswordCode(ForgetPasswordCode $forgetPasswordCode): self
    {
        if (!$this->forgetPasswordCodes->contains($forgetPasswordCode)) {
            $this->forgetPasswordCodes[] = $forgetPasswordCode;
            $forgetPasswordCode->setUser($this);
        }

        return $this;
    }

    public function removeForgetPasswordCode(ForgetPasswordCode $forgetPasswordCode): self
    {
        if ($this->forgetPasswordCodes->contains($forgetPasswordCode)) {
            $this->forgetPasswordCodes->removeElement($forgetPasswordCode);
            // set the owning side to null (unless already changed)
            if ($forgetPasswordCode->getUser() === $this) {
                $forgetPasswordCode->setUser(null);
            }
        }

        return $this;
    }
}
