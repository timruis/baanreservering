<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageVisitRepository")
 */
class PageVisit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CurrentUrl;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->Time;
    }

    public function setTime(\DateTimeInterface $Time): self
    {
        $this->Time = $Time;

        return $this;
    }

    public function getCurrentUrl(): ?string
    {
        return $this->CurrentUrl;
    }

    public function setCurrentUrl(string $CurrentUrl): self
    {
        $this->CurrentUrl = $CurrentUrl;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->User;
    }

    public function setUser(?user $User): self
    {
        $this->User = $User;

        return $this;
    }
}
