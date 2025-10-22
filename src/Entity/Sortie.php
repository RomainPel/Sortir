<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Participant $organisateur = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Site $siteOrganisateur = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'sortiesInscrit')]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): static
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getSiteOrganisateur(): ?Site
    {
        return $this->siteOrganisateur;
    }

    public function setSiteOrganisateur(?Site $siteOrganisateur): static
    {
        $this->siteOrganisateur = $siteOrganisateur;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addSortieInscrit($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeSortiesInscrit($this);
        }

        return $this;
    }
}
