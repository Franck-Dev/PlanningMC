<?php

namespace App\Entity;

use App\Repository\EqOTRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EqOTRepository::class)
 */
class EqOT
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Descriptif;

    /**
     * @ORM\ManyToOne(targetEntity=outillages::class, inversedBy="eqOTs")
     */
    private $ArtOut;

    /**
     * @ORM\ManyToMany(targetEntity=progMoyens::class, inversedBy="eqOTs")
     */
    private $progPolym;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $PolymssTrait;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $Statut;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dateDebHS;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dateFinHS;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=DatasMoulage::class, mappedBy="eqOut")
     */
    private $datasMoulages;

    public function __construct()
    {
        $this->progPolym = new ArrayCollection();
        $this->datasMoulages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?int
    {
        return $this->ref;
    }

    public function setRef(int $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->Descriptif;
    }

    public function setDescriptif(?string $Descriptif): self
    {
        $this->Descriptif = $Descriptif;

        return $this;
    }

    public function getArtOut(): ?outillages
    {
        return $this->ArtOut;
    }

    public function setArtOut(?outillages $ArtOut): self
    {
        $this->ArtOut = $ArtOut;

        return $this;
    }

    /**
     * @return Collection<int, progMoyens>
     */
    public function getProgPolym(): Collection
    {
        return $this->progPolym;
    }

    public function addProgPolym(progMoyens $progPolym): self
    {
        if (!$this->progPolym->contains($progPolym)) {
            $this->progPolym[] = $progPolym;
        }

        return $this;
    }

    public function removeProgPolym(progMoyens $progPolym): self
    {
        $this->progPolym->removeElement($progPolym);

        return $this;
    }

    public function getPolymssTrait(): ?int
    {
        return $this->PolymssTrait;
    }

    public function setPolymssTrait(?int $PolymssTrait): self
    {
        $this->PolymssTrait = $PolymssTrait;

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

    public function getDateDebHS(): ?\DateTimeImmutable
    {
        return $this->dateDebHS;
    }

    public function setDateDebHS(?\DateTimeImmutable $dateDebHS): self
    {
        $this->dateDebHS = $dateDebHS;

        return $this;
    }

    public function getDateFinHS(): ?\DateTimeImmutable
    {
        return $this->dateFinHS;
    }

    public function setDateFinHS(?\DateTimeImmutable $dateFinHS): self
    {
        $this->dateFinHS = $dateFinHS;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, DatasMoulage>
     */
    public function getDatasMoulages(): Collection
    {
        return $this->datasMoulages;
    }

    public function addDatasMoulage(DatasMoulage $datasMoulage): self
    {
        if (!$this->datasMoulages->contains($datasMoulage)) {
            $this->datasMoulages[] = $datasMoulage;
            $datasMoulage->setEqOut($this);
        }

        return $this;
    }

    public function removeDatasMoulage(DatasMoulage $datasMoulage): self
    {
        if ($this->datasMoulages->removeElement($datasMoulage)) {
            // set the owning side to null (unless already changed)
            if ($datasMoulage->getEqOut() === $this) {
                $datasMoulage->setEqOut(null);
            }
        }

        return $this;
    }
}
