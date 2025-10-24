<?php

namespace App\Entity;

use App\Repository\ParticipantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantsRepository::class)]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 30)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom = null;

    #[ORM\Column(length: 15)]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column]
    private ?bool $administrateur = false;

    #[ORM\Column]
    private ?bool $actif = true;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Site $site = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur')]
    private Collection $sortiesOrganise;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sortiesInscrit;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoProfil = null;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortiesInscrit = new ArrayCollection();
    }

    // --- GETTERS / SETTERS ---
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;
        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;
        return $this;
    }

    // --- Méthodes de UserInterface / PasswordAuthenticatedUserInterface ---
    public function getUserIdentifier(): string
    {
        return $this->mail; // l'identifiant utilisé pour la connexion
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->administrateur) {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }

    public function getPassword(): ?string
    {
        return $this->motDePasse;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporairement, les effacer ici
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getSortie(): Collection
    {
        return $this->sorties;
    }

    public function addSortieOrganise(Sortie $sortieOrganise): static
    {
        if (!$this->sortiesOrganise->contains($sortieOrganise)) {
            $this->sortiesOrganise->add($sortieOrganise);
            $sortieOrganise->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortieOrganise(Sortie $sortieOrganise): static
    {
        if ($this->sortiesOrganise->removeElement($sortieOrganise)) {
            // set the owning side to null (unless already changed)
            if ($sortieOrganise->getOrganisateur() === $this) {
                $sortieOrganise->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscrit(): Collection
    {
        return $this->sortiesInscrit;
    }

    public function addSortieInscrit(Sortie $sortieInscrit): static
    {
        if (!$this->sortiesInscrit->contains($sortieInscrit)) {
            $this->sortiesInscrit->add($sortieInscrit);
        }

        return $this;
    }

    public function removeSortieInscrit(Sortie $sortieInscrit): static
    {
        $this->sortiesInscrit->removeElement($sortieInscrit);

        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }
}
