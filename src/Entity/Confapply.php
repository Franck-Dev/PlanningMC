<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfapplyRepository")
 */
class Confapply
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
    private $TitreMenu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TitreSmenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $TitreSsmenu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreMenu(): ?string
    {
        return $this->TitreMenu;
    }

    public function setTitreMenu(string $TitreMenu): self
    {
        $this->TitreMenu = $TitreMenu;

        return $this;
    }

    public function getTitreSmenu(): ?string
    {
        return $this->TitreSmenu;
    }

    public function setTitreSmenu(string $TitreSmenu): self
    {
        $this->TitreSmenu = $TitreSmenu;

        return $this;
    }

    public function getTitreSsmenu(): ?string
    {
        return $this->TitreSsmenu;
    }

    public function setTitreSsmenu(?string $TitreSsmenu): self
    {
        $this->TitreSsmenu = $TitreSsmenu;

        return $this;
    }
}
