<?php
// src/Entity/Ressource.php
namespace App\Entity;

use App\Enum\RessourceType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Projet::class, inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projet $projet = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: [RessourceType::class, 'values'])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $quantite = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $heures = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unite = null;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero]
    private ?float $coutUnitaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    // Getters et setters
    public function getId(): ?int { return $this->id; }
    public function getProjet(): ?Projet { return $this->projet; }
    public function setProjet(?Projet $projet): self { $this->projet = $projet; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getQuantite(): ?float { return $this->quantite; }
    public function setQuantite(?float $quantite): self { $this->quantite = $quantite; return $this; }
    public function getHeures(): ?float { return $this->heures; }
    public function setHeures(?float $heures): self { $this->heures = $heures; return $this; }
    public function getUnite(): ?string { return $this->unite; }
    public function setUnite(?string $unite): self { $this->unite = $unite; return $this; }
    public function getCoutUnitaire(): ?float { return $this->coutUnitaire; }
    public function setCoutUnitaire(float $coutUnitaire): self { $this->coutUnitaire = $coutUnitaire; return $this; }
    public function getRole(): ?string { return $this->role; }
    public function setRole(?string $role): self { $this->role = $role; return $this; }
    
    public function getCoutTotal(): float
    {
        if ($this->type === RessourceType::HUMAIN->value) {
            return $this->heures * $this->coutUnitaire;
        } else {
            return ($this->quantite ?? 1) * $this->coutUnitaire;
        }
    }
    
    public function getDetails(): string
    {
        return match ($this->type) {
            RessourceType::HUMAIN->value => "{$this->heures} heures",
            RessourceType::MATERIEL->value => "{$this->quantite} unités",
            RessourceType::MATIERE->value => "{$this->quantite} {$this->unite}",
            default => '',
        };
    }
}