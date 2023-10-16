<?php

namespace App\Entity;

use App\Repository\ProgAvionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgAvionsRepository::class)
 */
class ProgAvions
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $iri;

    /**
     * @ORM\Column(type="integer")
     */
    private $idApi;

    /**
     * @ORM\ManyToMany(targetEntity=ProgMoyens::class, mappedBy="avion")
     */
    private $progMoyens;

    /**
     * @ORM\OneToMany(targetEntity=Outillages::class, mappedBy="Projet")
     */
    private $outillages;

    /**
     * @ORM\OneToMany(targetEntity=DatasMoulage::class, mappedBy="programme")
     */
    private $datasMoulages;

    public function __construct()
    {
        $this->progMoyens = new ArrayCollection();
        $this->outillages = new ArrayCollection();
        $this->datasMoulages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getIri(): ?string
    {
        return $this->iri;
    }

    public function setIri(string $iri): self
    {
        $this->iri = $iri;

        return $this;
    }

    public function getIdApi(): ?int
    {
        return $this->idApi;
    }

    public function setIdApi(int $idApi): self
    {
        $this->idApi = $idApi;

        return $this;
    }

    /**
     * @return Collection<int, ProgMoyens>
     */
    public function getProgMoyens(): Collection
    {
        return $this->progMoyens;
    }

    public function addProgMoyen(ProgMoyens $progMoyen): self
    {
        if (!$this->progMoyens->contains($progMoyen)) {
            $this->progMoyens[] = $progMoyen;
            $progMoyen->addAvion($this);
        }

        return $this;
    }

    public function removeProgMoyen(ProgMoyens $progMoyen): self
    {
        if ($this->progMoyens->removeElement($progMoyen)) {
            $progMoyen->removeAvion($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Outillages>
     */
    public function getOutillages(): Collection
    {
        return $this->outillages;
    }

    public function addOutillage(Outillages $outillage): self
    {
        if (!$this->outillages->contains($outillage)) {
            $this->outillages[] = $outillage;
            $outillage->setProjet($this);
        }

        return $this;
    }

    public function removeOutillage(Outillages $outillage): self
    {
        if ($this->outillages->removeElement($outillage)) {
            // set the owning side to null (unless already changed)
            if ($outillage->getProjet() === $this) {
                $outillage->setProjet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DatasMoulage>
     */
    public function getDatasMoulages(): Collection
    {
        return $this->datasMoulages;
    }

    public function addDatasMoulage(DatasMoulage $datasMoulage): self
    {
        if (!$this->datasMoulages->contains($datasMoulage)) {
            $this->datasMoulages[] = $datasMoulage;
            $datasMoulage->setProgramme($this);
        }

        return $this;
    }

    public function removeDatasMoulage(DatasMoulage $datasMoulage): self
    {
        if ($this->datasMoulages->removeElement($datasMoulage)) {
            // set the owning side to null (unless already changed)
            if ($datasMoulage->getProgramme() === $this) {
                $datasMoulage->setProgramme(null);
            }
        }

        return $this;
    }
}
