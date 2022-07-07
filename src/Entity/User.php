<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Ceci n'est pas une adresse mail valide!!'")
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Minimum de 8 caracteres")
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Minimum de 8 caracteres")
     */
    private $poste;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Minimum de 8 caracteres")
     */

    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Le mot de passe doit etre identique")
     */

    public $confirm_password;

    /**
     * @ORM\Column(type="datetime")
     */


    private $createdAt;

    /**
     * @ORM\Column(type="json")
     */
    private $Roles = [];

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=NomEquipe::class, inversedBy="Collaborateurs")
     */
    private $nomEquipe;

    /**
     * @ORM\OneToMany(targetEntity=NomEquipe::class, mappedBy="Manager")
     */
    private $nomEquipes;

    public function __toString(): string
    {
        return (string) $this->getUsername();
    }
      
    public function __construct()
    {
        $this->isActive = true;
        $this->nomEquipes = new ArrayCollection();
    }

    public function hydrate(array $datas)
    {
        foreach ($datas as $key => $value)
        {$methodSet = 'set'.ucfirst($key);
            if (method_exists($this, $methodSet))
            {
                switch ($key) {
                    case 'createdAt':
                        $this->$methodSet(new \DateTime($value));
                        break;
                    case 'poste':
                        $this->$methodSet($value['libelle']);
                        break;
                    case 'service':
                        $this->$methodSet($value['nom']);
                        break;
                    default:
                    $this->$methodSet($value);
                        break;
                }
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): ?int
    {
        return $this->id=$id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(String $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPoste(): ?string
    {

         return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function eraseCredentials(){}

    public function getSalt(){}

    public function getRoles()
    {
        //dump($this->Roles);
        if (empty($this->Roles)) {
             return ['ROLE_USER'];
         }
         return $this->Roles;
    }

    public function setRoles(array $Roles): self
    {
        $this->Roles = $Roles;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }

    public function getNomEquipe(): ?NomEquipe
    {
        return $this->nomEquipe;
    }

    public function setNomEquipe(?NomEquipe $nomEquipe): self
    {
        $this->nomEquipe = $nomEquipe;

        return $this;
    }

    /**
     * @return Collection|NomEquipe[]
     */
    public function getNomEquipes(): Collection
    {
        return $this->nomEquipes;
    }

    public function addNomEquipe(NomEquipe $nomEquipe): self
    {
        if (!$this->nomEquipes->contains($nomEquipe)) {
            $this->nomEquipes[] = $nomEquipe;
            $nomEquipe->setManager($this);
        }

        return $this;
    }

    public function removeNomEquipe(NomEquipe $nomEquipe): self
    {
        if ($this->nomEquipes->removeElement($nomEquipe)) {
            // set the owning side to null (unless already changed)
            if ($nomEquipe->getManager() === $this) {
                $nomEquipe->setManager(null);
            }
        }

        return $this;
    }

}
