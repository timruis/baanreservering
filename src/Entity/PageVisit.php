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
     * @ORM\Column(type="string", length=255)
     */
    private $CurrentUrl;

    /**
     * @ORM\Column(type="time")
     */
    private $Time;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTime(): ?\DateTimeInterface
    {
        return $this->Time;
    }

    public function setTime(\DateTimeInterface $Time): self
    {
        $this->Time = $Time;

        return $this;
    }
}
