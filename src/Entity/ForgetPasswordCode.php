<?php

namespace App\Entity;

use App\Repository\ForgetPasswordCodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ForgetPasswordCodeRepository::class)
 */
class ForgetPasswordCode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $ValidateKey;

    /**
     * @ORM\Column(type="datetime")
     */
    private $ValidUntil;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="forgetPasswordCodes")
     */
    private $User;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValidateKey(): ?string
    {
        return $this->ValidateKey;
    }

    public function setValidateKey(string $ValidateKey): self
    {
        $this->ValidateKey = $ValidateKey;

        return $this;
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->ValidUntil;
    }

    public function setValidUntil(\DateTimeInterface $ValidUntil): self
    {
        $this->ValidUntil = $ValidUntil;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
