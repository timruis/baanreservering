<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainingRepository")
 */
class Training
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
    private $StartTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="trainings")
     */
    private $Teacher;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeacher(): ?User
    {
        return $this->Teacher;
    }

    public function setTeacher(?User $Teacher): self
    {
        $this->Teacher = $Teacher;

        return $this;
    }
}
