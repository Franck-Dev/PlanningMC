<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolymRealRepository")
 */
class PolymReal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $NomPolym;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DebPolym;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $Statut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FinPolym;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Planning", inversedBy="polymReal", cascade={"persist", "remove"})
     */
    private $PolymPlannif;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $Articles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Moyens", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Moyens;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProgMoyens", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Programmes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPolym(): ?string
    {
        return $this->NomPolym;
    }

    public function setNomPolym(string $NomPolym): self
    {
        $this->NomPolym = $NomPolym;

        return $this;
    }

    public function getDebPolym(): ?\DateTimeInterface
    {
        return $this->DebPolym;
    }

    public function setDebPolym(\DateTimeInterface $DebPolym): self
    {
        $this->DebPolym = $DebPolym;

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

    public function getFinPolym(): ?\DateTimeInterface
    {
        return $this->FinPolym;
    }

    public function setFinPolym(?\DateTimeInterface $FinPolym): self
    {
        $this->FinPolym = $FinPolym;

        return $this;
    }

    public function getPolymPlannif(): ?Planning
    {
        return $this->PolymPlannif;
    }

    public function setPolymPlannif(?Planning $PolymPlannif): self
    {
        $this->PolymPlannif = $PolymPlannif;

        return $this;
    }

    public function getArticles(): ?array
    {
        return $this->Articles;
    }

    public function setArticles(?array $Articles): self
    {
        $this->Articles = $Articles;

        return $this;
    }

    public function getMoyens(): ?Moyens
    {
        return $this->Moyens;
    }

    public function setMoyens(Moyens $Moyens): self
    {
        $this->Moyens = $Moyens;

        return $this;
    }

    public function getProgrammes(): ?ProgMoyens
    {
        return $this->Programmes;
    }

    public function setProgrammes(ProgMoyens $Programmes): self
    {
        $this->Programmes = $Programmes;

        return $this;
    }
}
