<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryMoyensRepository")
 */
class CategoryMoyens
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $Description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProgMoyens", mappedBy="CateMoyen")
     */
    private $progMoyen;

    public function __toString(): string
    {
        return (string) $this->getLibelle();
    }

    public function __construct()
    {
        $this->progMoyen = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection|ProgMoyens[]
     */
    public function getProgMoyen(): Collection
    {
        return $this->progMoyen;
    }

    public function addProgMoyen(ProgMoyens $progMoyen): self
    {
        if (!$this->progMoyen->contains($progMoyen)) {
            $this->progMoyen[] = $progMoyen;
            $progMoyen->setCateMoyen($this);
        }

        return $this;
    }

    public function removeProgMoyen(ProgMoyens $progMoyen): self
    {
        if ($this->progMoyen->contains($progMoyen)) {
            $this->progMoyen->removeElement($progMoyen);
            // set the owning side to null (unless already changed)
            if ($progMoyen->getCateMoyen() === $this) {
                $progMoyen->setCateMoyen(null);
            }
        }

        return $this;
    }

}
