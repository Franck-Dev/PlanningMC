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
     * @ORM\Column(type="decimal", precision=2, scale=2, nullable=true)
     */
    private $Hauteur;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2, nullable=true)
     */
    private $Longueur;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=2, nullable=true)
     */
    private $Largeur;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $Volume;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $CoefAero;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProgMoyens", inversedBy="outillages",cascade={"persist"})
     */
    private $Programme;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Dispo;

    /**
     * @ORM\Column(type="time")
     */
    private $CycleMoulage;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $TpsDecharge;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $TpsCharge;

    /**
     * @ORM\Column(type="integer")
     */
    private $NbThermocouples;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ChargFige", mappedBy="OT")
     */
    private $chargFiges;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Articles", mappedBy="OutMoulage", cascade={"persist"})
     */
    private $articles;

    /**
     * @ORM\Column(type="smallint")
     */
    private $NbIndus;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DateDispo;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nbPolymssTrait;

    /**
     * @ORM\ManyToMany(targetEntity=Chargement::class, mappedBy="Outillages")
     */
    private $chargements;

    public function __construct()
    {
        $this->Programme = new ArrayCollection();
        $this->chargFiges = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->chargements = new ArrayCollection();
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
    public function getProgramme(): Collection
    {
        return $this->Programme;
    }

    public function addProgramme(ProgMoyens $programme): self
    {
        if (!$this->Programme->contains($programme)) {
            $this->Programme[] = $programme;
            
        }

        return $this;
    }

    public function removeProgramme(ProgMoyens $programme): self
    {
        if ($this->Programme->contains($programme)) {
            $this->Programme->removeElement($programme);
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

    public function getTpsDecharge(): ?\DateTimeInterface
    {
        return $this->TpsDecharge;
    }

    public function setTpsDecharge(?\DateTimeInterface $TpsDecharge): self
    {
        $this->TpsDecharge = $TpsDecharge;

        return $this;
    }

    public function getTpsCharge(): ?\DateTimeInterface
    {
        return $this->TpsCharge;
    }

    public function setTpsCharge(?\DateTimeInterface $TpsCharge): self
    {
        $this->TpsCharge = $TpsCharge;

        return $this;
    }

    public function getNbThermocouples(): ?int
    {
        return $this->NbThermocouples;
    }

    public function setNbThermocouples(int $NbThermocouples): self
    {
        $this->NbThermocouples = $NbThermocouples;

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
            $chargFige->addOT($this);
        }

        return $this;
    }

    public function removeChargFige(ChargFige $chargFige): self
    {
        if ($this->chargFiges->contains($chargFige)) {
            $this->chargFiges->removeElement($chargFige);
            $chargFige->removeOT($this);
        }

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addOutMoulage($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            $article->removeOutMoulage($this);
        }

        return $this;
    }

    public function getNbIndus(): ?int
    {
        return $this->NbIndus;
    }

    public function setNbIndus(int $NbIndus): self
    {
        $this->NbIndus = $NbIndus;

        return $this;
    }

    public function getDateDispo(): ?\DateTimeInterface
    {
        return $this->DateDispo;
    }

    public function setDateDispo(?\DateTimeInterface $DateDispo): self
    {
        $this->DateDispo = $DateDispo;

        return $this;
    }

    public function getNbPolymssTrait(): ?int
    {
        return $this->nbPolymssTrait;
    }

    public function setNbPolymssTrait(?int $nbPolymssTrait): self
    {
        $this->nbPolymssTrait = $nbPolymssTrait;

        return $this;
    }

    /**
     * @return Collection<int, Chargement>
     */
    public function getChargements(): Collection
    {
        return $this->chargements;
    }

    public function addChargement(Chargement $chargement): self
    {
        if (!$this->chargements->contains($chargement)) {
            $this->chargements[] = $chargement;
            $chargement->addOutillage($this);
        }

        return $this;
    }

    public function removeChargement(Chargement $chargement): self
    {
        if ($this->chargements->removeElement($chargement)) {
            $chargement->removeOutillage($this);
        }

        return $this;
    }
}
