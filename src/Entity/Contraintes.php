<?php

namespace App\Entity;

use App\Repository\ContraintesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContraintesRepository::class)
 */
class Contraintes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=54)
     */
    private $Libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\ManyToOne(targetEntity=services::class, inversedBy="contraintes")
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $impact;

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

    public function getService(): ?services
    {
        return $this->service;
    }

    public function setService(?services $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getImpact(): ?string
    {
        return $this->impact;
    }

    public function setImpact(string $impact): self
    {
        $this->impact = $impact;

        return $this;
    }
}
