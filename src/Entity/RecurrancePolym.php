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
     * @ORM\OneToOne(targetEntity="App\Entity\Planning", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $NumPlanning;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TypeRecurrance", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $TypeRecurrance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateFinrecurrance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $NumHeritage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumPlanning(): ?Planning
    {
        return $this->NumPlanning;
    }

    public function setNumPlanning(Planning $NumPlanning): self
    {
        $this->NumPlanning = $NumPlanning;

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

    public function getNumHeritage(): ?int
    {
        return $this->NumHeritage;
    }

    public function setNumHeritage(?int $NumHeritage): self
    {
        $this->NumHeritage = $NumHeritage;

        return $this;
    }
}
