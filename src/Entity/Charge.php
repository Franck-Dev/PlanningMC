<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChargeRepository")
 */
class Charge
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $OrdreFab;

    /**
     * @ORM\Column(type="integer")
     */
    private $PosteW;

    /**
     * @ORM\Column(type="integer")
     */
    private $ReferencePcs;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $DesignationPcs;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $NumProg;

    /**
     * @ORM\Column(type="integer")
     */
    private $Conf;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DateDeb;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateFin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateCreation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $Statut;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Chargement", inversedBy="OF", cascade={"persist", "remove"})
     */
    private $chargement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DatePilote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PolymReal", inversedBy="charges")
     */
    private $Polym;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ficheInstruc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $outillage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $equipement;

    /**
     * @ORM\ManyToOne(targetEntity=Demandes::class, cascade={"persist", "remove"}, inversedBy="ListOF")
     */
    private $demandes;

    public function __toString(): string
    {
        return (string) $this->getOrdreFab().$this->getDesignationPcs();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdreFab(): ?int
    {
        return $this->OrdreFab;
    }

    public function setOrdreFab(int $OrdreFab): self
    {
        $this->OrdreFab = $OrdreFab;

        return $this;
    }

    public function getPosteW(): ?int
    {
        return $this->PosteW;
    }

    public function setPosteW(int $PosteW): self
    {
        $this->PosteW = $PosteW;

        return $this;
    }

    public function getReferencePcs(): ?int
    {
        return $this->ReferencePcs;
    }

    public function setReferencePcs(int $ReferencePcs): self
    {
        $this->ReferencePcs = $ReferencePcs;

        return $this;
    }

    public function getDesignationPcs(): ?string
    {
        return $this->DesignationPcs;
    }

    public function setDesignationPcs(string $DesignationPcs): self
    {
        $this->DesignationPcs = $DesignationPcs;

        return $this;
    }

    public function getNumProg(): ?string
    {
        return $this->NumProg;
    }

    public function setNumProg(?string $NumProg): self
    {
        $this->NumProg = $NumProg;

        return $this;
    }

    public function getConf(): ?int
    {
        return $this->Conf;
    }

    public function setConf(int $Conf): self
    {
        $this->Conf = $Conf;

        return $this;
    }

    public function getDateDeb(): ?\DateTimeInterface
    {
        return $this->DateDeb;
    }

    public function setDateDeb(?\DateTimeInterface $DateDeb): self
    {
        $this->DateDeb = $DateDeb;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(?\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

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

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(?string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getChargement(): ?Chargement
    {
        return $this->chargement;
    }

    public function setChargement(?Chargement $chargement): self
    {
        $this->chargement = $chargement;

        return $this;
    }

    public function getDatePilote(): ?\DateTimeInterface
    {
        return $this->DatePilote;
    }

    public function setDatePilote(\DateTimeInterface $DatePilote): self
    {
        $this->DatePilote = $DatePilote;

        return $this;
    }

    public function getPolym(): ?PolymReal
    {
        return $this->Polym;
    }

    public function setPolym(?PolymReal $Polym): self
    {
        $this->Polym = $Polym;

        return $this;
    }

    public function getFicheInstruc(): ?string
    {
        return $this->ficheInstruc;
    }

    public function setFicheInstruc(?string $ficheInstruc): self
    {
        $this->ficheInstruc = $ficheInstruc;

        return $this;
    }

    public function getOutillage(): ?string
    {
        return $this->outillage;
    }

    public function setOutillage(?string $outillage): self
    {
        $this->outillage = $outillage;

        return $this;
    }

    public function getEquipement(): ?string
    {
        return $this->equipement;
    }

    public function setEquipement(?string $equipement): self
    {
        $this->equipement = $equipement;

        return $this;
    }

    public function getDemandes(): ?Demandes
    {
        return $this->demandes;
    }

    public function setDemandes(?Demandes $demandes): self
    {
        $this->demandes = $demandes;

        return $this;
    }
}
