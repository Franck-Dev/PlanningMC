<?php

namespace App\Entity;

use App\Repository\OrgaEqRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrgaEqRepository::class)
 */
class OrgaEq
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=NomEquipe::class, cascade={"persist", "remove"})
     */
    private $NomEquipe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $TypeW;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateFin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEquipe(): ?NomEquipe
    {
        return $this->NomEquipe;
    }

    public function setNomEquipe(?NomEquipe $NomEquipe): self
    {
        $this->NomEquipe = $NomEquipe;

        return $this;
    }

    public function getTypeW(): ?string
    {
        return $this->TypeW;
    }

    public function setTypeW(?string $TypeW): self
    {
        $this->TypeW = $TypeW;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }
}
