<?php

namespace App\Entity;

use App\Repository\MoulageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MoulageRepository::class)
 */
class Moulage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=outillages::class, cascade={"persist", "remove"})
     */
    private $outillage;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $DebMoul;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $FinMoul;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $DateLimiteMoulage;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $DateLimitePolym;

    /**
     * @ORM\OneToMany(targetEntity=charge::class, mappedBy="moulage")
     */
    private $of;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Mouleur;

    /**
     * @ORM\ManyToMany(targetEntity=Moyens::class, inversedBy="moulages")
     */
    private $Moyen;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Statut;

    public function __construct()
    {
        $this->of = new ArrayCollection();
        $this->Moyen = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOutillage(): ?outillages
    {
        return $this->outillage;
    }

    public function setOutillage(?outillages $outillage): self
    {
        $this->outillage = $outillage;

        return $this;
    }

    public function getDebMoul(): ?\DateTimeImmutable
    {
        return $this->DebMoul;
    }

    public function setDebMoul(?\DateTimeImmutable $DebMoul): self
    {
        $this->DebMoul = $DebMoul;

        return $this;
    }

    public function getFinMoul(): ?\DateTimeImmutable
    {
        return $this->FinMoul;
    }

    public function setFinMoul(?\DateTimeImmutable $FinMoul): self
    {
        $this->FinMoul = $FinMoul;

        return $this;
    }

    public function getDateLimiteMoulage(): ?\DateTimeImmutable
    {
        return $this->DateLimiteMoulage;
    }

    public function setDateLimiteMoulage(?\DateTimeImmutable $DateLimiteMoulage): self
    {
        $this->DateLimiteMoulage = $DateLimiteMoulage;

        return $this;
    }

    public function getDateLimitePolym(): ?\DateTimeImmutable
    {
        return $this->DateLimitePolym;
    }

    public function setDateLimitePolym(?\DateTimeImmutable $DateLimitePolym): self
    {
        $this->DateLimitePolym = $DateLimitePolym;

        return $this;
    }

    /**
     * @return Collection<int, charge>
     */
    public function getOf(): Collection
    {
        return $this->of;
    }

    public function addOf(charge $of): self
    {
        if (!$this->of->contains($of)) {
            $this->of[] = $of;
            $of->setMoulage($this);
        }

        return $this;
    }

    public function removeOf(charge $of): self
    {
        if ($this->of->removeElement($of)) {
            // set the owning side to null (unless already changed)
            if ($of->getMoulage() === $this) {
                $of->setMoulage(null);
            }
        }

        return $this;
    }

    public function getMouleur(): ?string
    {
        return $this->Mouleur;
    }

    public function setMouleur(?string $Mouleur): self
    {
        $this->Mouleur = $Mouleur;

        return $this;
    }

    /**
     * @return Collection<int, Moyens>
     */
    public function getMoyen(): Collection
    {
        return $this->Moyen;
    }

    public function addMoyen(Moyens $moyen): self
    {
        if (!$this->Moyen->contains($moyen)) {
            $this->Moyen[] = $moyen;
        }

        return $this;
    }

    public function removeMoyen(Moyens $moyen): self
    {
        $this->Moyen->removeElement($moyen);

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }
}
