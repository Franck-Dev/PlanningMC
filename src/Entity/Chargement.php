<?php

namespace App\Entity;

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
    private $Nom_Chargement;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdPlanning;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomChargement(): ?string
    {
        return $this->Nom_Chargement;
    }

    public function setNomChargement(string $Nom_Chargement): self
    {
        $this->Nom_Chargement = $Nom_Chargement;

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
}
