<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CaractMoyensRepository")
 */
class CaractMoyens
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Id_CatMoyen;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCatMoyen(): ?int
    {
        return $this->Id_CatMoyen;
    }

    public function setIdCatMoyen(int $Id_CatMoyen): self
    {
        $this->Id_CatMoyen = $Id_CatMoyen;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }
}
