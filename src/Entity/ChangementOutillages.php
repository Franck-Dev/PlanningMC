<?php

namespace App\Entity;

use App\Repository\ChangementOutillagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChangementOutillagesRepository::class)
 */
class ChangementOutillages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Planning::class, inversedBy="changementOutillages")
     */
    private $prodImpacte;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $dateDeb;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $dateFin;

    /**
     * @ORM\ManyToOne(targetEntity=progMoyens::class, inversedBy="changementOutillages")
     */
    private $prog;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $modifedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProdImpacte(): ?Planning
    {
        return $this->prodImpacte;
    }

    public function setProdImpacte(?Planning $prodImpacte): self
    {
        $this->prodImpacte = $prodImpacte;

        return $this;
    }

    public function getDateDeb(): ?\DateTimeImmutable
    {
        return $this->dateDeb;
    }

    public function setDateDeb(\DateTimeImmutable $dateDeb): self
    {
        $this->dateDeb = $dateDeb;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getProg(): ?progMoyens
    {
        return $this->prog;
    }

    public function setProg(?progMoyens $prog): self
    {
        $this->prog = $prog;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifedAt(): ?\DateTimeImmutable
    {
        return $this->modifedAt;
    }

    public function setModifedAt(?\DateTimeImmutable $modifedAt): self
    {
        $this->modifedAt = $modifedAt;

        return $this;
    }
}
