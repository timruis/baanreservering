<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourtReservationRepository")
 */
class CourtReservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Court;

    /**
     * @ORM\Column(type="integer")
     */
    private $Players;

    /**
     * @ORM\Column(type="datetime")
     */
    private $StartTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Players")
     */
    private $Player;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="CourtReservationsTeam")
     */
    private $OtherPlayers;

    public function __construct()
    {
        $this->OtherPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourt(): ?int
    {
        return $this->Court;
    }

    public function setCourt(int $Court): self
    {
        $this->Court = $Court;

        return $this;
    }

    public function getPlayers(): ?int
    {
        return $this->Players;
    }

    public function setPlayers(int $Players): self
    {
        $this->Players = $Players;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->StartTime;
    }

    public function setStartTime(\DateTimeInterface $StartTime): self
    {
        $this->StartTime = $StartTime;

        return $this;
    }

    public function getPlayTimeAmount(): ?int
    {
        return $this->playTimeAmount;
    }

    public function setPlayTimeAmount(int $playTimeAmount): self
    {
        $this->playTimeAmount = $playTimeAmount;

        return $this;
    }

    public function getPlayer(): ?User
    {
        return $this->Player;
    }

    public function setPlayer(?User $Player): self
    {
        $this->Player = $Player;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getOtherPlayers(): Collection
    {
        return $this->OtherPlayers;
    }

    public function addOtherPlayer(User $otherPlayer): self
    {
        if (!$this->OtherPlayers->contains($otherPlayer)) {
            $this->OtherPlayers[] = $otherPlayer;
        }

        return $this;
    }

    public function removeOtherPlayer(User $otherPlayer): self
    {
        if ($this->OtherPlayers->contains($otherPlayer)) {
            $this->OtherPlayers->removeElement($otherPlayer);
        }

        return $this;
    }
}
