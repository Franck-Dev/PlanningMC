<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolymCreaRepository")
 */
class PolymCrea
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $Moyens;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Planning", inversedBy="polymCrea", cascade={"persist", "remove"})
     */
    private $PolymPlannif;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $NumPolym;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateExe;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateValid;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF1;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF2;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF3;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF4;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF5;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF6;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF7;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF8;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF9;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF10;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF11;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF12;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF13;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF14;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF15;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF16;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF17;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF18;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF19;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF20;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF21;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF22;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF23;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF24;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $OF25;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Commentaires;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $Programme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Validation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoyens(): ?string
    {
        return $this->Moyens;
    }

    public function setMoyens(string $Moyens): self
    {
        $this->Moyens = $Moyens;

        return $this;
    }

    public function getPolymPlannif(): ?Planning
    {
        return $this->PolymPlannif;
    }

    public function setPolymPlannif(?Planning $PolymPlannif): self
    {
        $this->PolymPlannif = $PolymPlannif;

        return $this;
    }

    public function getNumPolym(): ?string
    {
        return $this->NumPolym;
    }

    public function setNumPolym(string $NumPolym): self
    {
        $this->NumPolym = $NumPolym;

        return $this;
    }

    public function getDateExe(): ?\DateTimeInterface
    {
        return $this->DateExe;
    }

    public function setDateExe(\DateTimeInterface $DateExe): self
    {
        $this->DateExe = $DateExe;

        return $this;
    }

    public function getDateValid(): ?\DateTimeInterface
    {
        return $this->DateValid;
    }

    public function setDateValid(\DateTimeInterface $DateValid): self
    {
        $this->DateValid = $DateValid;

        return $this;
    }

    public function getOF1(): ?string
    {
        return $this->OF1;
    }

    public function setOF1(?string $OF1): self
    {
        $this->OF1 = $OF1;

        return $this;
    }

    public function getOF2(): ?string
    {
        return $this->OF2;
    }

    public function setOF2(?string $OF2): self
    {
        $this->OF2 = $OF2;

        return $this;
    }

    public function getOF3(): ?string
    {
        return $this->OF3;
    }

    public function setOF3(?string $OF3): self
    {
        $this->OF3 = $OF3;

        return $this;
    }

    public function getOF4(): ?string
    {
        return $this->OF4;
    }

    public function setOF4(?string $OF4): self
    {
        $this->OF4 = $OF4;

        return $this;
    }

    public function getOF5(): ?string
    {
        return $this->OF5;
    }

    public function setOF5(?string $OF5): self
    {
        $this->OF5 = $OF5;

        return $this;
    }

    public function getOF6(): ?string
    {
        return $this->OF6;
    }

    public function setOF6(?string $OF6): self
    {
        $this->OF6 = $OF6;

        return $this;
    }

    public function getOF7(): ?string
    {
        return $this->OF7;
    }

    public function setOF7(?string $OF7): self
    {
        $this->OF7 = $OF7;

        return $this;
    }

    public function getOF8(): ?string
    {
        return $this->OF8;
    }

    public function setOF8(?string $OF8): self
    {
        $this->OF8 = $OF8;

        return $this;
    }

    public function getOF9(): ?string
    {
        return $this->OF9;
    }

    public function setOF9(?string $OF9): self
    {
        $this->OF9 = $OF9;

        return $this;
    }

    public function getOF10(): ?string
    {
        return $this->OF10;
    }

    public function setOF10(?string $OF10): self
    {
        $this->OF10 = $OF10;

        return $this;
    }

    public function getOF11(): ?string
    {
        return $this->OF11;
    }

    public function setOF11(?string $OF11): self
    {
        $this->OF11 = $OF11;

        return $this;
    }

    public function getOF12(): ?string
    {
        return $this->OF12;
    }

    public function setOF12(?string $OF12): self
    {
        $this->OF12 = $OF12;

        return $this;
    }

    public function getOF13(): ?string
    {
        return $this->OF13;
    }

    public function setOF13(?string $OF13): self
    {
        $this->OF13 = $OF13;

        return $this;
    }

    public function getOF14(): ?string
    {
        return $this->OF14;
    }

    public function setOF14(?string $OF14): self
    {
        $this->OF14 = $OF14;

        return $this;
    }

    public function getOF15(): ?string
    {
        return $this->OF15;
    }

    public function setOF15(?string $OF15): self
    {
        $this->OF15 = $OF15;

        return $this;
    }

    public function getOF16(): ?string
    {
        return $this->OF16;
    }

    public function setOF16(?string $OF16): self
    {
        $this->OF16 = $OF16;

        return $this;
    }

    public function getOF17(): ?string
    {
        return $this->OF17;
    }

    public function setOF17(?string $OF17): self
    {
        $this->OF17 = $OF17;

        return $this;
    }

    public function getOF18(): ?string
    {
        return $this->OF18;
    }

    public function setOF18(?string $OF18): self
    {
        $this->OF18 = $OF18;

        return $this;
    }

    public function getOF19(): ?string
    {
        return $this->OF19;
    }

    public function setOF19(?string $OF19): self
    {
        $this->OF19 = $OF19;

        return $this;
    }

    public function getOF20(): ?string
    {
        return $this->OF20;
    }

    public function setOF20(?string $OF20): self
    {
        $this->OF20 = $OF20;

        return $this;
    }

    public function getOF21(): ?string
    {
        return $this->OF21;
    }

    public function setOF21(?string $OF21): self
    {
        $this->OF21 = $OF21;

        return $this;
    }

    public function getOF22(): ?string
    {
        return $this->OF22;
    }

    public function setOF22(?string $OF22): self
    {
        $this->OF22 = $OF22;

        return $this;
    }

    public function getOF23(): ?string
    {
        return $this->OF23;
    }

    public function setOF23(?string $OF23): self
    {
        $this->OF23 = $OF23;

        return $this;
    }

    public function getOF24(): ?string
    {
        return $this->OF24;
    }

    public function setOF24(?string $OF24): self
    {
        $this->OF24 = $OF24;

        return $this;
    }

    public function getOF25(): ?string
    {
        return $this->OF25;
    }

    public function setOF25(?string $OF25): self
    {
        $this->OF25 = $OF25;

        return $this;
    }

    public function getCommentaires(): ?string
    {
        return $this->Commentaires;
    }

    public function setCommentaires(?string $Commentaires): self
    {
        $this->Commentaires = $Commentaires;

        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->Programme;
    }

    public function setProgramme(string $Programme): self
    {
        $this->Programme = $Programme;

        return $this;
    }

    public function getValidation(): ?string
    {
        return $this->Validation;
    }

    public function setValidation(?string $Validation): self
    {
        $this->Validation = $Validation;

        return $this;
    }
}
