<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeRepository")
 */
class Demande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProgMoyens", inversedBy="demande")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_polym;

    /**
     * @ORM\Column(type="date")
     */
    private $date_propose;

    /**
     * @ORM\Column(type="time")
     */
    private $heure_propose;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $outillage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPolym(): ?ProgMoyen
    {
        return $this->id_polym;
    }

    public function setIdPolym(?ProgMoyen $id_polym): self
    {
        $this->polym = $id_polym;

        return $this;
    }

    public function getDatePropose(): ?\DateTimeInterface
    {
        return $this->date_propose;
    }

    public function setDatePropose(\DateTimeInterface $date_propose): self
    {
        $this->date_propose = $date_propose;

        return $this;
    }

    public function getHeurePropose(): ?\DateTimeInterface
    {
        return $this->heure_propose;
    }

    public function setHeurePropose(\DateTimeInterface $heure_propose): self
    {
        $this->heure_propose = $heure_propose;

        return $this;
    }

    public function getOutillage(): ?string
    {
        return $this->outillage;
    }

    public function setOutillage(string $outillage): self
    {
        $this->outillage = $outillage;

        return $this;
    }
}
