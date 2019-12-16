<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChargeRepository")
 */
class Charge
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
    private $OrdreFab;

    /**
     * @ORM\Column(type="integer")
     */
    private $PosteW;

    /**
     * @ORM\Column(type="integer")
     */
    private $ReferencePcs;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $DesignationPcs;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $NumProg;

    /**
     * @ORM\Column(type="integer")
     */
    private $Conf;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateDebW;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateFinW;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateCreation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $Statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdreFab(): ?int
    {
        return $this->OrdreFab;
    }

    public function setOrdreFab(int $OrdreFab): self
    {
        $this->OrdreFab = $OrdreFab;

        return $this;
    }

    public function getPosteW(): ?int
    {
        return $this->PosteW;
    }

    public function setPosteW(int $PosteW): self
    {
        $this->PosteW = $PosteW;

        return $this;
    }

    public function getReferencePcs(): ?int
    {
        return $this->ReferencePcs;
    }

    public function setReferencePcs(int $ReferencePcs): self
    {
        $this->ReferencePcs = $ReferencePcs;

        return $this;
    }

    public function getDesignationPcs(): ?string
    {
        return $this->DesignationPcs;
    }

    public function setDesignationPcs(string $DesignationPcs): self
    {
        $this->DesignationPcs = $DesignationPcs;

        return $this;
    }

    public function getNumProg(): ?string
    {
        return $this->NumProg;
    }

    public function setNumProg(?string $NumProg): self
    {
        $this->NumProg = $NumProg;

        return $this;
    }

    public function getConf(): ?int
    {
        return $this->Conf;
    }

    public function setConf(int $Conf): self
    {
        $this->Conf = $Conf;

        return $this;
    }

    public function getDateDebW(): ?\DateTimeInterface
    {
        return $this->DateDebW;
    }

    public function setDateDebW(?\DateTimeInterface $DateDebW): self
    {
        $this->DateDebW = $DateDebW;

        return $this;
    }

    public function getDateFinW(): ?\DateTimeInterface
    {
        return $this->DateFinW;
    }

    public function setDateFinW(?\DateTimeInterface $DateFinW): self
    {
        $this->DateFinW = $DateFinW;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): self
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(?string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }
}
