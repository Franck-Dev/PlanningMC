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

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outillages", mappedBy="Programme")
     */
    private $outillages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ChargFige", mappedBy="Programme")
     */
    private $chargFiges;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $codeAvion= [];

    public function __toString(): string
    {
        return (string) $this->getNom();
    }

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
        $this->outillages = new ArrayCollection();
        $this->chargFiges = new ArrayCollection();
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

    /**
     * @return Collection|Outillages[]
     */
    public function getOutillages(): Collection
    {
        return $this->outillages;
    }

    public function addOutillage(Outillages $outillage): self
    {
        if (!$this->outillages->contains($outillage)) {
            $this->outillages[] = $outillage;
            $outillage->addProgramme($this);
        }

        return $this;
    }

    public function removeOutillage(Outillages $outillage): self
    {
        if ($this->outillages->contains($outillage)) {
            $this->outillages->removeElement($outillage);
            $outillage->removeProgramme($this);
        }

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
            $chargFige->setProgramme($this);
        }

        return $this;
    }

    public function removeChargFige(ChargFige $chargFige): self
    {
        if ($this->chargFiges->contains($chargFige)) {
            $this->chargFiges->removeElement($chargFige);
            // set the owning side to null (unless already changed)
            if ($chargFige->getProgramme() === $this) {
                $chargFige->setProgramme(null);
            }
        }

        return $this;
    }

    public function getCodeAvion(): ?array
    {
        return $this->codeAvion;
    }

    public function setCodeAvion(?array $codeAvion): self
    {
        $this->codeAvion = $codeAvion;

        return $this;
    }

}
