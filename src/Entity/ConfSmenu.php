<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfSmenuRepository")
 */
class ConfSmenu
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
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu5;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu6;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdSmenu7;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getIdSmenu1(): ?string
    {
        return $this->IdSmenu1;
    }

    public function setIdSmenu1(?string $IdSmenu1): self
    {
        $this->IdSmenu1 = $IdSmenu1;

        return $this;
    }

    public function getIdSmenu2(): ?string
    {
        return $this->IdSmenu2;
    }

    public function setIdSmenu2(?string $IdSmenu2): self
    {
        $this->IdSmenu2 = $IdSmenu2;

        return $this;
    }

    public function getIdSmenu3(): ?string
    {
        return $this->IdSmenu3;
    }

    public function setIdSmenu3(?string $IdSmenu3): self
    {
        $this->IdSmenu3 = $IdSmenu3;

        return $this;
    }

    public function getIdSmenu4(): ?string
    {
        return $this->IdSmenu4;
    }

    public function setIdSmenu4(?string $IdSmenu4): self
    {
        $this->IdSmenu4 = $IdSmenu4;

        return $this;
    }

    public function getIdSmenu5(): ?string
    {
        return $this->IdSmenu5;
    }

    public function setIdSmenu5(?string $IdSmenu5): self
    {
        $this->IdSmenu5 = $IdSmenu5;

        return $this;
    }

    public function getIdSmenu6(): ?string
    {
        return $this->IdSmenu6;
    }

    public function setIdSmenu6(?string $IdSmenu6): self
    {
        $this->IdSmenu6 = $IdSmenu6;

        return $this;
    }

    public function getIdSmenu7(): ?string
    {
        return $this->IdSmenu7;
    }

    public function setIdSmenu7(?string $IdSmenu7): self
    {
        $this->IdSmenu7 = $IdSmenu7;

        return $this;
    }
}
