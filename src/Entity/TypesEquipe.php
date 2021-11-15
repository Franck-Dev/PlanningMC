<?php

namespace App\Entity;

use App\Repository\TypesEquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypesEquipeRepository::class)
 */
class TypesEquipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="array")
     */
    private $Jourw = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $QteHeureW;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $BackgroundColor;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $TextColor;

    /**
     * @ORM\OneToMany(targetEntity=NomEquipe::class, mappedBy="OrgaW")
     */
    private $nomEquipes;

    public function __toString(): string
    {
        return (string) $this->getNom();
    }
    
    public function __construct()
    {
        $this->nomEquipes = new ArrayCollection();
    }

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

    public function getJourw(): ?array
    {
        return $this->Jourw;
    }

    public function setJourw(array $Jourw): self
    {
        $this->Jourw = $Jourw;

        return $this;
    }

    public function getQteHeureW(): ?int
    {
        return $this->QteHeureW;
    }

    public function setQteHeureW(int $QteHeureW): self
    {
        $this->QteHeureW = $QteHeureW;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->BackgroundColor;
    }

    public function setBackgroundColor(?string $BackgroundColor): self
    {
        $this->BackgroundColor = $BackgroundColor;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->TextColor;
    }

    public function setTextColor(?string $TextColor): self
    {
        $this->TextColor = $TextColor;

        return $this;
    }

    /**
     * @return Collection|NomEquipe[]
     */
    public function getNomEquipes(): Collection
    {
        return $this->nomEquipes;
    }

    public function addNomEquipe(NomEquipe $nomEquipe): self
    {
        if (!$this->nomEquipes->contains($nomEquipe)) {
            $this->nomEquipes[] = $nomEquipe;
            $nomEquipe->setOrgaW($this);
        }

        return $this;
    }

    public function removeNomEquipe(NomEquipe $nomEquipe): self
    {
        if ($this->nomEquipes->removeElement($nomEquipe)) {
            // set the owning side to null (unless already changed)
            if ($nomEquipe->getOrgaW() === $this) {
                $nomEquipe->setOrgaW(null);
            }
        }

        return $this;
    }
}
