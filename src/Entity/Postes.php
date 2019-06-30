<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostesRepository")
 */
class Postes
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
    private $Libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="role")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="roles")
     */
    private $NbUser;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->NbUser = new ArrayCollection();
    }

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getNbUser(): Collection
    {
        return $this->NbUser;
    }

    public function addNbUser(User $nbUser): self
    {
        if (!$this->NbUser->contains($nbUser)) {
            $this->NbUser[] = $nbUser;
            $nbUser->setRoles($this);
        }

        return $this;
    }

    public function removeNbUser(User $nbUser): self
    {
        if ($this->NbUser->contains($nbUser)) {
            $this->NbUser->removeElement($nbUser);
            // set the owning side to null (unless already changed)
            if ($nbUser->getRoles() === $this) {
                $nbUser->setRoles(null);
            }
        }

        return $this;
    }
}
