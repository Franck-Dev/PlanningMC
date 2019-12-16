<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanningRepository")
 */
class Planning
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
    private $Identification;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Action;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DebutDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FinDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Statut;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Demandes", inversedBy="planning", cascade={"persist"})
     */
    private $NumDemande;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PolymReal", mappedBy="PolymPlannif", cascade={"persist", "remove"})
     */
    private $polymReal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PolymCrea", mappedBy="PolymPlannif", cascade={"persist", "remove"})
     */
    private $polymCrea;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\RecurrancePolym", mappedBy="NumPlanning", cascade={"persist", "remove"})
     */
    private $recurrancePolym;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentification(): ?string
    {
        return $this->Identification;
    }

    public function setIdentification(string $Identification): self
    {
        $this->Identification = $Identification;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->Action;
    }

    public function setAction(string $Action): self
    {
        $this->Action = $Action;

        return $this;
    }

    public function getDebutDate(): ?\DateTimeInterface
    {
        return $this->DebutDate;
    }

    public function setDebutDate(\DateTimeInterface $DebutDate): self
    {
        $this->DebutDate = $DebutDate;

        return $this;
    }

    public function getFinDate(): ?\DateTimeInterface
    {
        return $this->FinDate;
    }

    public function setFinDate(\DateTimeInterface $FinDate): self
    {
        $this->FinDate = $FinDate;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getNumDemande(): ?Demandes
    {
        return $this->NumDemande;
    }

    public function setNumDemande(?Demandes $NumDemande): self
    {
        $this->NumDemande = $NumDemande;

        return $this;
    }

    public function getPolymReal(): ?PolymReal
    {
        return $this->polymReal;
    }

    public function setPolymReal(?PolymReal $polymReal): self
    {
        $this->polymReal = $polymReal;

        // set (or unset) the owning side of the relation if necessary
        $newPolymPlannif = $polymReal === null ? null : $this;
        if ($newPolymPlannif !== $polymReal->getPolymPlannif()) {
            $polymReal->setPolymPlannif($newPolymPlannif);
        }

        return $this;
    }

    public function getPolymCrea(): ?PolymCrea
    {
        return $this->polymCrea;
    }

    public function setPolymCrea(?PolymCrea $polymCrea): self
    {
        $this->polymCrea = $polymCrea;

        // set (or unset) the owning side of the relation if necessary
        $newPolymPlannif = $polymCrea === null ? null : $this;
        if ($newPolymPlannif !== $polymCrea->getPolymPlannif()) {
            $polymCrea->setPolymPlannif($newPolymPlannif);
        }

        return $this;
    }

    public function getRecurrancePolym(): ?RecurrancePolym
    {
        return $this->recurrancePolym;
    }

    public function setRecurrancePolym(RecurrancePolym $recurrancePolym): self
    {
        $this->recurrancePolym = $recurrancePolym;

        // set the owning side of the relation if necessary
        if ($this !== $recurrancePolym->getNumPlanning()) {
            $recurrancePolym->setNumPlanning($this);
        }

        return $this;
    }
}
