<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProgPolymRepository")
 */
class ProgPolym
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
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Tps_Theo;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $Duree;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $Tps_Charg;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $Tps_Decharg;

    /**
     * @ORM\Column(type="integer")
     */
    private $Id_Moyen;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Thermocouples;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $SPC;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
     private $Date_Creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
     private $Date_Modif;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

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

    public function getTpsTheo(): ?string
    {
        return $this->Tps_Theo;
    }

    public function setTpsTheo(?string $Tps_Theo): self
    {
        $this->Tps_Theo = $Tps_Theo;

        return $this;
    }

    public function getDuree(): ?\datetime
    {
        return $this->Duree;
    }

    public function setDuree(\datetime $Duree): self
    {
        $this->Duree = $Duree;

        return $this;
    }

    public function getTpsCharg(): ?\datetime
    {
        return $this->Tps_Charg;
    }

    public function setTpsCharg(?\datetime $Tps_Charg): self
    {
        $this->Tps_Charg = $Tps_Charg;

        return $this;
    }

    public function getTpsDecharg(): ?\datetime
    {
        return $this->Tps_Decharg;
    }

    public function setTpsDecharg(?\datetime $Tps_Decharg): self
    {
        $this->Tps_Decharg = $Tps_Decharg;

        return $this;
    }

    public function getIdMoyen(): ?int
    {
        return $this->Id_Moyen;
    }

    public function setIdMoyen(int $Id_Moyen): self
    {
        $this->Id_Moyen = $Id_Moyen;

        return $this;
    }

    public function getThermocouples(): ?bool
    {
        return $this->Thermocouples;
    }

    public function setThermocouples(bool $Thermocouples): self
    {
        $this->Thermocouples = $Thermocouples;

        return $this;
    }

    public function getSPC(): ?bool
    {
        return $this->SPC;
    }

    public function setSPC(?bool $SPC): self
    {
        $this->SPC = $SPC;

        return $this;
    }

    public function getDateCreation(): ?\datetime
    {
        return $this->Date_Creation;
    }

    public function setDateCreation(?\datetime $Date_Creation): self
    {
        $this->Date_Creation = $Date_Creation;

        return $this;
    }

    public function getDateModif(): ?\datetime
    {
        return $this->Date_Modif;
    }

    public function setDateModif(?\datetime $Date_Modif): self
    {
        $this->Date_Modif = $Date_Modif;

        return $this;
    }
}
