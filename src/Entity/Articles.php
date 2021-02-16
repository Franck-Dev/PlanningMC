<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticlesRepository")
 */
class Articles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Range(
     *      min = "7000000",
     *      max = "7999999")
     * @ORM\Column(type="integer")
     */
    private $Reference;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $Designation;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outillages", inversedBy="articles")
     */
    private $OutMoulage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Serie;

    public function __construct()
    {
        $this->OutMoulage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?int
    {
        return $this->Reference;
    }

    public function setReference(int $Reference): self
    {
        $this->Reference = $Reference;

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

    /**
     * @return Collection|Outillages[]
     */
    public function getOutMoulage(): Collection
    {
        return $this->OutMoulage;
    }

    public function addOutMoulage(Outillages $outMoulage): self
    {
        if (!$this->OutMoulage->contains($outMoulage)) {
            $this->OutMoulage[] = $outMoulage;
        }

        return $this;
    }

    public function removeOutMoulage(Outillages $outMoulage): self
    {
        if ($this->OutMoulage->contains($outMoulage)) {
            $this->OutMoulage->removeElement($outMoulage);
        }

        return $this;
    }

    public function getSerie(): ?bool
    {
        return $this->Serie;
    }

    public function setSerie(bool $Serie): self
    {
        $this->Serie = $Serie;

        return $this;
    }
}
