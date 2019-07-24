<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutillagesRepository")
 */
class Outillages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $Ref;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $Designation;

    /**
     * @ORM\Column(type="integer")
     */
    private $NbEmpreinte;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Hauteur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Longueur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Largeur;

    /**
     * @ORM\Column(type="integer")
     */
    private $Volume;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $CoefAero;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProgMoyens", inversedBy="outillages")
     */
    private $Programme1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Dispo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CycleMoulage;

    public function __construct()
    {
        $this->Programme1 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->Ref;
    }

    public function setRef(string $Ref): self
    {
        $this->Ref = $Ref;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->Designation;
    }

    public function setDesignation(string $Designation): self
    {
        $this->Designation = $Designation;

        return $this;
    }

    public function getNbEmpreinte(): ?int
    {
        return $this->NbEmpreinte;
    }

    public function setNbEmpreinte(int $NbEmpreinte): self
    {
        $this->NbEmpreinte = $NbEmpreinte;

        return $this;
    }

    public function getHauteur(): ?int
    {
        return $this->Hauteur;
    }

    public function setHauteur(?int $Hauteur): self
    {
        $this->Hauteur = $Hauteur;

        return $this;
    }

    public function getLongueur(): ?int
    {
        return $this->Longueur;
    }

    public function setLongueur(?int $Longueur): self
    {
        $this->Longueur = $Longueur;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->Largeur;
    }

    public function setLargeur(?int $Largeur): self
    {
        $this->Largeur = $Largeur;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->Volume;
    }

    public function setVolume(int $Volume): self
    {
        $this->Volume = $Volume;

        return $this;
    }

    public function getCoefAero()
    {
        return $this->CoefAero;
    }

    public function setCoefAero($CoefAero): self
    {
        $this->CoefAero = $CoefAero;

        return $this;
    }

    /**
     * @return Collection|ProgMoyens[]
     */
    public function getProgramme1(): Collection
    {
        return $this->Programme1;
    }

    public function addProgramme1(ProgMoyens $programme1): self
    {
        if (!$this->Programme1->contains($programme1)) {
            $this->Programme1[] = $programme1;
        }

        return $this;
    }

    public function removeProgramme1(ProgMoyens $programme1): self
    {
        if ($this->Programme1->contains($programme1)) {
            $this->Programme1->removeElement($programme1);
        }

        return $this;
    }

    public function getDispo(): ?bool
    {
        return $this->Dispo;
    }

    public function setDispo(bool $Dispo): self
    {
        $this->Dispo = $Dispo;

        return $this;
    }

    public function getCycleMoulage(): ?\DateTimeInterface
    {
        return $this->CycleMoulage;
    }

    public function setCycleMoulage(\DateTimeInterface $CycleMoulage): self
    {
        $this->CycleMoulage = $CycleMoulage;

        return $this;
    }
}
