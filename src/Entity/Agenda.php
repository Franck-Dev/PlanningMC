<?php

namespace App\Entity;

use App\Repository\AgendaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgendaRepository::class)
 */
class Agenda
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
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
     * @ORM\Column(type="datetime")
     */
    private $DateDeb;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateFin;

    /**
     * @ORM\Column(type="integer")
     */
    private $TpsAlloue;

    /**
     * @ORM\ManyToOne(targetEntity=OrgaEq::class, inversedBy="agendas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $NomOrgaW;

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

    public function getDateDeb(): ?\DateTimeInterface
    {
        return $this->DateDeb;
    }

    public function setDateDeb(\DateTimeInterface $DateDeb): self
    {
        $this->DateDeb = $DateDeb;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(?\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getTpsAlloue(): ?int
    {
        return $this->TpsAlloue;
    }

    public function setTpsAlloue(int $TpsAlloue): self
    {
        $this->TpsAlloue = $TpsAlloue;

        return $this;
    }

    public function getNomOrgaW(): ?OrgaEq
    {
        return $this->NomOrgaW;
    }

    public function setNomOrgaW(?OrgaEq $NomOrgaW): self
    {
        $this->NomOrgaW = $NomOrgaW;

        return $this;
    }
}
