<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChargementRepository")
 */
class Chargement
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
    private $NomChargement;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdPlanning;

    /**
     * @ORM\Column(type="date")
     */
    private $DatePlannif;

    /**
     * @ORM\Column(type="integer")
     */
    private $Remplissage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Charge", mappedBy="chargement", fetch="EAGER")
     */
    private $OF;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Programme;

    /**
     * @ORM\ManyToMany(targetEntity=Outillages::class, inversedBy="chargements", fetch="EAGER")
     */
    private $Outillages;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $TpsChargeOT;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $txChargeOT;

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function __construct()
    {
        $this->OF = new ArrayCollection();
        $this->Outillages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomChargement(): ?string
    {
        return $this->NomChargement;
    }

    public function setNomChargement(string $NomChargement): self
    {
        $this->NomChargement = $NomChargement;

        return $this;
    }

    public function getIdPlanning(): ?int
    {
        return $this->IdPlanning;
    }

    public function setIdPlanning(int $IdPlanning): self
    {
        $this->IdPlanning = $IdPlanning;

        return $this;
    }

    public function getDatePlannif(): ?\DateTimeInterface
    {
        return $this->DatePlannif;
    }

    public function setDatePlannif(\DateTimeInterface $DatePlannif): self
    {
        $this->DatePlannif = $DatePlannif;

        return $this;
    }

    public function getRemplissage(): ?int
    {
        return $this->Remplissage;
    }

    public function setRemplissage(int $Remplissage): self
    {
        $this->Remplissage = $Remplissage;

        return $this;
    }

    /**
     * @return Collection|charge[]
     */
    public function getOF(): Collection
    {
        return $this->OF;
    }

    public function addOF(charge $oF): self
    {
        if (!$this->OF->contains($oF)) {
            $this->OF[] = $oF;
            $oF->setChargement($this);
        }

        return $this;
    }

    public function removeOF(charge $oF): self
    {
        if ($this->OF->contains($oF)) {
            $this->OF->removeElement($oF);
            // set the owning side to null (unless already changed)
            if ($oF->getChargement() === $this) {
                $oF->setChargement(null);
            }
        }

        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->Programme;
    }

    public function setProgramme(string $Programme): self
    {
        $this->Programme = $Programme;

        return $this;
    }

    /**
     * @return Collection<int, Outillages>
     */
    public function getOutillages(): Collection
    {
        return $this->Outillages;
    }

    public function addOutillage(Outillages $outillage): self
    {
        if (!$this->Outillages->contains($outillage)) {
            $this->Outillages[] = $outillage;
        }

        return $this;
    }

    public function removeOutillage(Outillages $outillage): self
    {
        $this->Outillages->removeElement($outillage);

        return $this;
    }

    public function getTpsChargeOT(): ?\DateInterval
    {
        return $this->TpsChargeOT;
    }

    public function setTpsChargeOT(?\DateInterval $TpsChargeOT): self
    {
        $this->TpsChargeOT = $TpsChargeOT;

        return $this;
    }

    public function getTxChargeOT(): ?int
    {
        return $this->txChargeOT;
    }

    public function setTxChargeOT(?int $txChargeOT): self
    {
        $this->txChargeOT = $txChargeOT;

        return $this;
    }
}
