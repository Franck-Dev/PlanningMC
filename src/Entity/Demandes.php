<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandesRepository")
 */
class Demandes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProgMoyens", inversedBy="demandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Cycle;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DatePropose;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $HeurePropose;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Outillages;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Plannifie;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $commentaires;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moyens", inversedBy="demandes")
     */
    private $MoyenUtilise;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateHeureFin;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Planning", mappedBy="NumDemande", cascade={"persist", "remove"})
     */
    private $planning;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Reccurance;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $UserCrea;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $UserModif;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $RecurValide;

    /**
     * @ORM\OneToMany(targetEntity=Charge::class, mappedBy="demandes")
     */
    private $ListOF;

    public function __construct()
    {
        $this->ListOF = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCycle(): ?ProgMoyens
    {
        return $this->Cycle;
    }

    public function setCycle(?ProgMoyens $Cycle): self
    {
        $this->Cycle = $Cycle;

        return $this;
    }

    public function getDatePropose(): ?\DateTimeInterface
    {
        return $this->DatePropose;
    }

    public function setDatePropose(\DateTimeInterface $DatePropose): self
    {
        $this->DatePropose = $DatePropose;

        return $this;
    }

    public function getHeurePropose(): ?\DateTimeInterface
    {
        return $this->HeurePropose;
    }

    public function setHeurePropose(?\DateTimeInterface $HeurePropose): self
    {
        $this->HeurePropose = $HeurePropose;

        return $this;
    }

    public function getOutillages(): ?string
    {
        return $this->Outillages;
    }

    public function setOutillages(?string $Outillages): self
    {
        $this->Outillages = $Outillages;

        return $this;
    }

    public function getPlannifie(): ?bool
    {
        return $this->Plannifie;
    }

    public function setPlannifie(bool $Plannifie): self
    {
        $this->Plannifie = $Plannifie;

        return $this;
    }

    public function getCommentaires(): ?string
    {
        return $this->commentaires;
    }

    public function setCommentaires(?string $commentaires): self
    {
        $this->commentaires = $commentaires;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(?\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getMoyenUtilise(): ?Moyens
    {
        return $this->MoyenUtilise;
    }

    public function setMoyenUtilise(?Moyens $MoyenUtilise): self
    {
        $this->MoyenUtilise = $MoyenUtilise;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->DateHeureFin;
    }

    public function setDateHeureFin(?\DateTimeInterface $DateHeureFin): self
    {
        $this->DateHeureFin = $DateHeureFin;

        return $this;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;

        // set (or unset) the owning side of the relation if necessary
        $newNumDemande = $planning === null ? null : $this;
        if ($newNumDemande !== $planning->getNumDemande()) {
            $planning->setNumDemande($newNumDemande);
        }

        return $this;
    }

    public function getReccurance(): ?bool
    {
        return $this->Reccurance;
    }

    public function setReccurance(bool $Reccurance): self
    {
        $this->Reccurance = $Reccurance;

        return $this;
    }

    public function getUserCrea(): ?String
    {
        return $this->UserCrea;
    }

    public function setUserCrea(String $UserCrea): self
    {
        $this->UserCrea = $UserCrea;

        return $this;
    }

    public function getUserModif(): ?String
    {
        return $this->UserModif;
    }

    public function setUserModif(?String $UserModif): self
    {
        $this->UserModif = $UserModif;

        return $this;
    }

    public function getRecurValide(): ?bool
    {
        return $this->RecurValide;
    }

    public function setRecurValide(?bool $RecurValide): self
    {
        $this->RecurValide = $RecurValide;

        return $this;
    }

     /**
     * @return Collection|Charge[]
     */
    public function getListOF(): Collection
    {
        return $this->ListOF;
    }

    public function addListOF(Charge $listOF): self
    {
        if (!$this->ListOF->contains($listOF)) {
            $this->ListOF[] = $listOF;
            $listOF->setDemandes($this);
        }

        return $this;
    }

    public function removeListOF(Charge $listOF): self
    {
        if ($this->ListOF->contains($listOF)) {
            $this->ListOF->removeElement($listOF);
            // set the owning side to null (unless already changed)
            if ($listOF->getDemandes() === $this) {
                $listOF->setDemandes(null);
            }
        }

        return $this;
    }
}
