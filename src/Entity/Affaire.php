<?php

namespace App\Entity;

use App\Enum\AffaireStatus;
use App\Enum\AffaireType;
use App\Repository\AffaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AffaireRepository::class)]
class Affaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    private ?string $titre = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: [AffaireType::class, 'values'])]
    private ?string $type = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: [AffaireStatus::class, 'values'])]
    private ?string $status = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le client est obligatoire")]
    private ?string $client = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: "Le montant doit être positif")]
    private ?float $montant = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $resultat = null;

    #[ORM\OneToOne(targetEntity: Projet::class, cascade: ['persist', 'remove'], mappedBy: 'affaire')]
    private ?Projet $projet = null;

    public function __construct()
    {
        $this->dateDebut = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): self { $this->dateDebut = $dateDebut; return $this; }
    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): self { $this->dateFin = $dateFin; return $this; }
    public function getClient(): ?string { return $this->client; }
    public function setClient(string $client): self { $this->client = $client; return $this; }
    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(?float $montant): self { $this->montant = $montant; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getResultat(): ?string { return $this->resultat; }
    public function setResultat(?string $resultat): self { $this->resultat = $resultat; return $this; }
    public function getProjet(): ?Projet { return $this->projet; }
    
    public function setProjet(?Projet $projet): self
    {
        if ($projet !== null && $projet->getAffaire() !== $this) {
            $projet->setAffaire($this);
        }
        $this->projet = $projet;
        return $this;
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            AffaireType::BC->value => 'Bon de commande',
            AffaireType::AO->value => 'Appel d\'offres',
            default => $this->type,
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            AffaireStatus::EN_COURS->value => 'En cours',
            AffaireStatus::ADJUDICATAIRE->value => 'Adjudicataire',
            AffaireStatus::PERDU->value => 'Perdu',
            AffaireStatus::ABANDONNE->value => 'Abandonné',
            default => $this->status,
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            AffaireStatus::ADJUDICATAIRE->value => 'bg-success',
            AffaireStatus::PERDU->value => 'bg-danger',
            AffaireStatus::ABANDONNE->value => 'bg-warning',
            default => 'bg-info',
        };
    }
}