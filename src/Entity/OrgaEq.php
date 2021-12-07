<?php

namespace App\Entity;

use App\Repository\OrgaEqRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $DateDebut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateFin;

    /**
     * @ORM\OneToMany(targetEntity=Agenda::class, mappedBy="NomOrgaW", orphanRemoval=true)
     */
    private $agendas;

    public function __construct()
    {
        $this->agendas = new ArrayCollection();
    }

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
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): self
    {
        $this->DateDebut = $DateDebut;

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

    /**
     * @return Collection|Agenda[]
     */
    public function getAgendas(): Collection
    {
        return $this->agendas;
    }

    public function addAgenda(Agenda $agenda): self
    {
        if (!$this->agendas->contains($agenda)) {
            $this->agendas[] = $agenda;
            $agenda->setNomOrgaW($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agendas->removeElement($agenda)) {
            // set the owning side to null (unless already changed)
            if ($agenda->getNomOrgaW() === $this) {
                $agenda->setNomOrgaW(null);
            }
        }

        return $this;
    }
}
