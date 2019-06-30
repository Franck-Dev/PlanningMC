<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProgMoyensRepository")
 */
class ProgMoyens
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
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="integer")
     */
    private $TpsTheo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $TpsChargement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $TpsDechargement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CategoryMoyens", inversedBy="progMoyen")
     * @ORM\JoinColumn(nullable=false)
     */
    private $CateMoyen;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Thermocouples;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $SPC;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateCreation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateModif;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Demandes", mappedBy="Cycle")
     */
    private $demandes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $couleur;


    public function __construct()
    {
        $this->demandes = new ArrayCollection();
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

    public function getTpsTheo(): ?int
    {
        return $this->TpsTheo;
    }

    public function setTpsTheo(int $TpsTheo): self
    {
        $this->TpsTheo = $TpsTheo;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->Duree;
    }

    public function setDuree(\DateTimeInterface $Duree): self
    {
        $this->Duree = $Duree;

        return $this;
    }

    public function getTpsChargement(): ?\DateTimeInterface
    {
        return $this->TpsChargement;
    }

    public function setTpsChargement(\DateTimeInterface $TpsChargement): self
    {
        $this->TpsChargement = $TpsChargement;

        return $this;
    }

    public function getTpsDechargement(): ?\DateTimeInterface
    {
        return $this->TpsDechargement;
    }

    public function setTpsDechargement(\DateTimeInterface $TpsDechargement): self
    {
        $this->TpsDechargement = $TpsDechargement;

        return $this;
    }

    public function getCateMoyen(): ?CategoryMoyens
    {
        return $this->CateMoyen;
    }

    public function setCateMoyen(?CategoryMoyens $CateMoyen): self
    {
        $this->CateMoyen = $CateMoyen;

        return $this;
    }

    public function getThermocouples(): ?bool
    {
        return $this->Thermocouples;
    }

    public function setThermocouples(bool $Thermocouples): self
    {
        $this->Thermocouples = $Thermocouples;

        return $this;
    }

    public function getSPC(): ?bool
    {
        return $this->SPC;
    }

    public function setSPC(?bool $SPC): self
    {
        $this->SPC = $SPC;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): self
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->DateModif;
    }

    public function setDateModif(?\DateTimeInterface $DateModif): self
    {
        $this->DateModif = $DateModif;

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
            $demande->setCycle($this);
        }

        return $this;
    }

    public function removeDemande(Demandes $demande): self
    {
        if ($this->demandes->contains($demande)) {
            $this->demandes->removeElement($demande);
            // set the owning side to null (unless already changed)
            if ($demande->getCycle() === $this) {
                $demande->setCycle(null);
            }
        }

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

}
