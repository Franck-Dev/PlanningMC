<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MoyensRepository")
 */
class Moyens
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
    private $Libelle;

    /**
     * @ORM\Column(type="integer")
     */
    private $Id_Service;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="integer")
     */
    private $Id_Types_Caracteristiques;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $Date_Maintenance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Operationnel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CategoryMoyens", inversedBy="Moyens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoryMoyens;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Demandes", mappedBy="MoyenUtilise")
     */
    private $demandes;

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->Libelle;
    }

    public function setLibelle(string $Libelle): self
    {
        $this->Libelle = $Libelle;

        return $this;
    }


    public function getIdService(): ?int
    {
        return $this->Id_Service;
    }

    public function setIdService(int $Id_Service): self
    {
        $this->Id_Service = $Id_Service;

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

    public function getIdTypesCaracteristiques(): ?int
    {
        return $this->Id_Types_Caracteristiques;
    }

    public function setIdTypesCaracteristiques(int $Id_Types_Caracteristiques): self
    {
        $this->Id_Types_Caracteristiques = $Id_Types_Caracteristiques;

        return $this;
    }

    public function getDateMaintenance(): ?\DateTimeInterface
    {
        return $this->Date_Maintenance;
    }

    public function setDateMaintenance(?\DateTimeInterface $Date_Maintenance): self
    {
        $this->Date_Maintenance = $Date_Maintenance;

        return $this;
    }

    public function getOperationnel(): ?bool
    {
        return $this->Operationnel;
    }

    public function setOperationnel(bool $Operationnel): self
    {
        $this->Operationnel = $Operationnel;

        return $this;
    }

    public function getCategoryMoyens(): ?CategoryMoyens
    {
        return $this->categoryMoyens;
    }

    public function setCategoryMoyens(?CategoryMoyens $categoryMoyens): self
    {
        $this->categoryMoyens = $categoryMoyens;

        return $this;
    }

    /**
     * @return Collection|Demandes[]
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demandes $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setMoyenUtilise($this);
        }

        return $this;
    }

    public function removeDemande(Demandes $demande): self
    {
        if ($this->demandes->contains($demande)) {
            $this->demandes->removeElement($demande);
            // set the owning side to null (unless already changed)
            if ($demande->getMoyenUtilise() === $this) {
                $demande->setMoyenUtilise(null);
            }
        }

        return $this;
    }
}
