<?php

namespace App\Entity;

use App\Repository\NomEquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NomEquipeRepository::class)
 */
class NomEquipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\ManyToOne(targetEntity=TypesEquipe::class, inversedBy="nomEquipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $OrgaW;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="nomEquipe")
     */
    private $Collaborateurs;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="nomEquipes")
     */
    private $Manager;

    public function __toString(): string
    {
        return (string) $this->getNom();
    }

    public function __construct()
    {
        $this->Collaborateurs = new ArrayCollection();
    }

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

    public function getOrgaW(): ?TypesEquipe
    {
        return $this->OrgaW;
    }

    public function setOrgaW(?TypesEquipe $OrgaW): self
    {
        $this->OrgaW = $OrgaW;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getCollaborateurs(): Collection
    {
        return $this->Collaborateurs;
    }

    public function addCollaborateur(User $collaborateur): self
    {
        if (!$this->Collaborateurs->contains($collaborateur)) {
            $this->Collaborateurs[] = $collaborateur;
            $collaborateur->setNomEquipe($this);
        }

        return $this;
    }

    public function removeCollaborateur(User $collaborateur): self
    {
        if ($this->Collaborateurs->removeElement($collaborateur)) {
            // set the owning side to null (unless already changed)
            if ($collaborateur->getNomEquipe() === $this) {
                $collaborateur->setNomEquipe(null);
            }
        }

        return $this;
    }

    public function getManager(): ?user
    {
        return $this->Manager;
    }

    public function setManager(?user $Manager): self
    {
        $this->Manager = $Manager;

        return $this;
    }
}
