<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sorties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $noSortie = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTime $datedebut = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column]
    private ?\DateTime $datecloture = null;

    #[ORM\Column]
    private ?int $nbinscriptionmax = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $descriptioninfos = null;

    #[ORM\Column(nullable: true)]
    private ?int $etatsortie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoSortie(): ?int
    {
        return $this->noSortie;
    }

    public function setNoSortie(int $noSortie): static
    {
        $this->noSortie = $noSortie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDatedebut(): ?\DateTime
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTime $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDatecloture(): ?\DateTime
    {
        return $this->datecloture;
    }

    public function setDatecloture(\DateTime $datecloture): static
    {
        $this->datecloture = $datecloture;

        return $this;
    }

    public function getNbinscriptionmax(): ?int
    {
        return $this->nbinscriptionmax;
    }

    public function setNbinscriptionmax(int $nbinscriptionmax): static
    {
        $this->nbinscriptionmax = $nbinscriptionmax;

        return $this;
    }

    public function getDescriptioninfos(): ?string
    {
        return $this->descriptioninfos;
    }

    public function setDescriptioninfos(?string $descriptioninfos): static
    {
        $this->descriptioninfos = $descriptioninfos;

        return $this;
    }

    public function getEtatsortie(): ?int
    {
        return $this->etatsortie;
    }

    public function setEtatsortie(?int $etatsortie): static
    {
        $this->etatsortie = $etatsortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): static
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }
}
