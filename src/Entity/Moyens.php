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

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $Activitees;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ChargFige", mappedBy="Moyen")
     */
    private $chargFiges;

    /**
     * @ORM\ManyToMany(targetEntity=Moulage::class, mappedBy="Moyen")
     */
    private $moulages;

    public function __toString(): string
    {
        return (string) $this->getLibelle();
    }

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
        $this->chargFiges = new ArrayCollection();
        $this->moulages = new ArrayCollection();
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

    public function getActivitees(): ?string
    {
        return $this->Activitees;
    }

    public function setActivitees(?string $Activitees): self
    {
        $this->Activitees = $Activitees;

        return $this;
    }

    /**
     * @return Collection|ChargFige[]
     */
    public function getChargFiges(): Collection
    {
        return $this->chargFiges;
    }

    public function addChargFige(ChargFige $chargFige): self
    {
        if (!$this->chargFiges->contains($chargFige)) {
            $this->chargFiges[] = $chargFige;
            $chargFige->setMoyen($this);
        }

        return $this;
    }

    public function removeChargFige(ChargFige $chargFige): self
    {
        if ($this->chargFiges->contains($chargFige)) {
            $this->chargFiges->removeElement($chargFige);
            // set the owning side to null (unless already changed)
            if ($chargFige->getMoyen() === $this) {
                $chargFige->setMoyen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Moulage>
     */
    public function getMoulages(): Collection
    {
        return $this->moulages;
    }

    public function addMoulage(Moulage $moulage): self
    {
        if (!$this->moulages->contains($moulage)) {
            $this->moulages[] = $moulage;
            $moulage->addMoyen($this);
        }

        return $this;
    }

    public function removeMoulage(Moulage $moulage): self
    {
        if ($this->moulages->removeElement($moulage)) {
            $moulage->removeMoyen($this);
        }

        return $this;
    }
}
