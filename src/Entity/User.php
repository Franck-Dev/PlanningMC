<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields={"email"},
 *  message ="Cette adresse est dejà utilise"
 * )
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
     * @Assert\Email(message="Ceci n\'est pas une adresse mail valide!!'")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Services", inversedBy="NbUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Postes", inversedBy="NbUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poste;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Minimum de 8 caractères")
     */

    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Le mot de passe doit être identique")
     */

    public $confirm_password;

    /**
     * @ORM\Column(type="datetime")
     */


    private $DateCreation;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPoste(): ?Postes
    {

         return $this->poste;
    }

    public function setPoste(?Postes $poste): self
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): self
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function eraseCredentials(){}

    public function getSalt(){}

    public function getRoles()
    {
        if (empty($this->roles)) {
             return ['ROLE_USER'];
         }
         return $this->roles;
    }

}
