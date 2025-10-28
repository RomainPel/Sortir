<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[Assert\GreaterThan("today", message: "La date de la sortie doit être postérieure à aujourd'hui.")]
    #[ORM\Column]
    private ?\DateTime $dateDebut = null;

    #[Assert\PositiveOrZero]
    #[ORM\Column]
    private ?int $duree = 0;

    #[Assert\GreaterThan("today", message: "La date de clôture des inscriptions doit être postérieure à aujourd'hui.")]
    #[Assert\Expression("this.dateCloture < this.dateDebut", message: "La date de clôture des inscriptions doit être antérieur à la date de sortie.")]
    #[ORM\Column]
    private ?\DateTime $dateCloture = null;

    #[Assert\Positive]
    #[ORM\Column]
    private ?int $nbInscriptionMax = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $descriptionInfos = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Participant $organisateur = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
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

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

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

    public function getDateCloture(): ?\DateTime
    {
        return $this->dateCloture;
    }

    /**
     * @param \DateTime $dateCloture
     * @return $this
     */
    public function setDateCloture(\DateTime $dateCloture): static
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getDescriptioninfos(): ?string
    {
        return $this->descriptionInfos;
    }

    public function setDescriptionInfos(?string $descriptionInfos): static
    {
        $this->descriptionInfos = $descriptionInfos;

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
            $participant->removeSortieInscrit($this);
        }

        return $this;
    }
}
