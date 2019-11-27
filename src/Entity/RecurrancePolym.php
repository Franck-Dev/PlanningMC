<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecurrancePolymRepository")
 */
class RecurrancePolym
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Demandes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $NumDemande;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TypeRecurrance", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $TypeRecurrance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateFinrecurrance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumDemande(): ?Demandes
    {
        return $this->NumDemande;
    }

    public function setNumDemande(Demandes $NumDemande): self
    {
        $this->NumDemande = $NumDemande;

        return $this;
    }

    public function getTypeRecurrance(): ?TypeRecurrance
    {
        return $this->TypeRecurrance;
    }

    public function setTypeRecurrance(TypeRecurrance $TypeRecurrance): self
    {
        $this->TypeRecurrance = $TypeRecurrance;

        return $this;
    }

    public function getDateFinrecurrance(): ?\DateTimeInterface
    {
        return $this->DateFinrecurrance;
    }

    public function setDateFinrecurrance(\DateTimeInterface $DateFinrecurrance): self
    {
        $this->DateFinrecurrance = $DateFinrecurrance;

        return $this;
    }
}
