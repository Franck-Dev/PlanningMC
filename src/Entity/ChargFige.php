<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChargFigeRepository")
 */
class ChargFige
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $Code;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outillages", inversedBy="chargFiges")
     */
    private $OT;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moyens", inversedBy="chargFiges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Moyen;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Statut;

    /**
     * @ORM\Column(type="smallint")
     */
    private $Pourc;

    public function __construct()
    {
        $this->OT = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->Code;
    }

    public function setCode(string $Code): self
    {
        $this->Code = $Code;

        return $this;
    }

    /**
     * @return Collection|Outillages[]
     */
    public function getOT(): Collection
    {
        return $this->OT;
    }

    public function addOT(Outillages $oT): self
    {
        if (!$this->OT->contains($oT)) {
            $this->OT[] = $oT;
        }

        return $this;
    }

    public function removeOT(Outillages $oT): self
    {
        if ($this->OT->contains($oT)) {
            $this->OT->removeElement($oT);
        }

        return $this;
    }

    public function getMoyen(): ?Moyens
    {
        return $this->Moyen;
    }

    public function setMoyen(?Moyens $Moyen): self
    {
        $this->Moyen = $Moyen;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->Statut;
    }

    public function setStatut(bool $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getPourc(): ?int
    {
        return $this->Pourc;
    }

    public function setPourc(int $Pourc): self
    {
        $this->Pourc = $Pourc;

        return $this;
    }
}
