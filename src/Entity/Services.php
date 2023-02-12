<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServicesRepository")
 */
class Services
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
     * @ORM\Column(type="integer")
     */
    private $Id_Chef;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Nb_Pers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="services")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="service")
     */
    private $NbUser;

    public function __toString(): string
    {
        return (string) $this->getNom();
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->NbUser = new ArrayCollection();
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

    public function getIdChef(): ?int
    {
        return $this->Id_Chef;
    }

    public function setIdChef(int $Id_Chef): self
    {
        $this->Id_Chef = $Id_Chef;

        return $this;
    }

    public function getNbPers(): ?int
    {
        return $this->Nb_Pers;
    }

    public function setNbPers(?int $Nb_Pers): self
    {
        $this->Nb_Pers = $Nb_Pers;

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
            $user->setServices($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getServices() === $this) {
                $user->setServices(null);
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
            $nbUser->setService($this);
        }

        return $this;
    }

    public function removeNbUser(User $nbUser): self
    {
        if ($this->NbUser->contains($nbUser)) {
            $this->NbUser->removeElement($nbUser);
            // set the owning side to null (unless already changed)
            if ($nbUser->getService() === $this) {
                $nbUser->setService(null);
            }
        }

        return $this;
    }
}
