<?php

namespace App\Entity;

use App\Repository\DatasMoulageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DatasMoulageRepository::class)
 */
class DatasMoulage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $artGamm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DescriptifPhase;

    /**
     * @ORM\ManyToOne(targetEntity=EqOT::class, inversedBy="datasMoulages")
     */
    private $eqOut;

    /**
     * @ORM\ManyToOne(targetEntity=progMoyens::class, inversedBy="datasMoulages")
     */
    private $progLaser;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $cycleMoulage;

    /**
     * @ORM\ManyToOne(targetEntity=progMoyens::class, inversedBy="datasMoulages")
     */
    private $progPolym;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $debVal;

    /**
     * @ORM\ManyToOne(targetEntity=progAvions::class, inversedBy="datasMoulages")
     */
    private $programme;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $zoneTrav;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtGamm(): ?string
    {
        return $this->artGamm;
    }

    public function setArtGamm(string $artGamm): self
    {
        $this->artGamm = $artGamm;

        return $this;
    }

    public function getDescriptifPhase(): ?string
    {
        return $this->DescriptifPhase;
    }

    public function setDescriptifPhase(?string $DescriptifPhase): self
    {
        $this->DescriptifPhase = $DescriptifPhase;

        return $this;
    }

    public function getEqOut(): ?EqOT
    {
        return $this->eqOut;
    }

    public function setEqOut(?EqOT $eqOut): self
    {
        $this->eqOut = $eqOut;

        return $this;
    }

    public function getProgLaser(): ?progMoyens
    {
        return $this->progLaser;
    }

    public function setProgLaser(?progMoyens $progLaser): self
    {
        $this->progLaser = $progLaser;

        return $this;
    }

    public function getCycleMoulage(): ?\DateInterval
    {
        return $this->cycleMoulage;
    }

    public function setCycleMoulage(\DateInterval $cycleMoulage): self
    {
        $this->cycleMoulage = $cycleMoulage;

        return $this;
    }

    public function getProgPolym(): ?progMoyens
    {
        return $this->progPolym;
    }

    public function setProgPolym(?progMoyens $progPolym): self
    {
        $this->progPolym = $progPolym;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDebVal(): ?\DateTimeImmutable
    {
        return $this->debVal;
    }

    public function setDebVal(?\DateTimeImmutable $debVal): self
    {
        $this->debVal = $debVal;

        return $this;
    }

    public function getProgramme(): ?progAvions
    {
        return $this->programme;
    }

    public function setProgramme(?progAvions $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

    public function getZoneTrav(): ?string
    {
        return $this->zoneTrav;
    }

    public function setZoneTrav(?string $zoneTrav): self
    {
        $this->zoneTrav = $zoneTrav;

        return $this;
    }
}
