<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolymRealRepository")
 */
class PolymReal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $NomPolym;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DebPolym;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $Statut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FinPolym;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Planning", inversedBy="polymReal", cascade={"persist", "remove"})
     */
    private $PolymPlannif;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $Articles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Moyens", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Moyens;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProgMoyens", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Programmes;

    /**
     * @ORM\Column(type="integer")
     */
    private $NbrPcs;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $PourcVolCharge;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $Retard;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Charge", mappedBy="Polym")
     */
    private $charges;

    public function __construct()
    {
        $this->charges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPolym(): ?string
    {
        return $this->NomPolym;
    }

    public function setNomPolym(string $NomPolym): self
    {
        $this->NomPolym = $NomPolym;

        return $this;
    }

    public function getDebPolym(): ?\DateTimeInterface
    {
        return $this->DebPolym;
    }

    public function setDebPolym(\DateTimeInterface $DebPolym): self
    {
        $this->DebPolym = $DebPolym;

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

    public function getFinPolym(): ?\DateTimeInterface
    {
        return $this->FinPolym;
    }

    public function setFinPolym(?\DateTimeInterface $FinPolym): self
    {
        $this->FinPolym = $FinPolym;

        return $this;
    }

    public function getPolymPlannif(): ?Planning
    {
        return $this->PolymPlannif;
    }

    public function setPolymPlannif(?Planning $PolymPlannif): self
    {
        $this->PolymPlannif = $PolymPlannif;

        return $this;
    }

    public function getArticles(): ?array
    {
        return $this->Articles;
    }

    public function setArticles(?array $Articles): self
    {
        $this->Articles = $Articles;

        return $this;
    }

    public function getMoyens(): ?Moyens
    {
        return $this->Moyens;
    }

    public function setMoyens(Moyens $Moyens): self
    {
        $this->Moyens = $Moyens;

        return $this;
    }

    public function getProgrammes(): ?ProgMoyens
    {
        return $this->Programmes;
    }

    public function setProgrammes(ProgMoyens $Programmes): self
    {
        $this->Programmes = $Programmes;

        return $this;
    }

    public function getNbrPcs(): ?int
    {
        return $this->NbrPcs;
    }

    public function setNbrPcs(int $NbrPcs): self
    {
        $this->NbrPcs = $NbrPcs;

        return $this;
    }

    public function getPourcVolCharge(): ?int
    {
        return $this->PourcVolCharge;
    }

    public function setPourcVolCharge(?int $PourcVolCharge): self
    {
        $this->PourcVolCharge = $PourcVolCharge;

        return $this;
    }

    public function getRetard(): ?\DateTimeInterface
    {
        return $this->Retard;
    }

    public function setRetard(?\DateTimeInterface $Retard): self
    {
        $this->Retard = $Retard;

        return $this;
    }

    /**
     * @return Collection|Charge[]
     */
    public function getCharges(): Collection
    {
        return $this->charges;
    }

    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge->setPolym($this);
        }

        return $this;
    }

    public function removeCharge(Charge $charge): self
    {
        if ($this->charges->contains($charge)) {
            $this->charges->removeElement($charge);
            // set the owning side to null (unless already changed)
            if ($charge->getPolym() === $this) {
                $charge->setPolym(null);
            }
        }

        return $this;
    }
}
