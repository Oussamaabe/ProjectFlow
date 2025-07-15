<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'articles')]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $reference;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $designation;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private float $prixUnitaire;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $uniteMesure = null;

    #[ORM\ManyToOne(targetEntity: Fournisseur::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private $fournisseur;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $categorie = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $tva = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $actif = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;
    #[ORM\OneToOne(mappedBy: 'article', targetEntity: Stock::class, cascade: ['persist', 'remove'])]
    private ?Stock $stock = null;

    // Getter / Setter :
    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        $this->stock = $stock;

        if ($stock->getArticle() !== $this) {
            $stock->setArticle($this);
        }

        return $this;
    }


    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->dateModification = new \DateTime();
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getDesignation(): string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;
        return $this;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(float $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getUniteMesure(): ?string
    {
        return $this->uniteMesure;
    }

    public function setUniteMesure(?string $uniteMesure): self
    {
        $this->uniteMesure = $uniteMesure;
        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    // Méthodes utilitaires

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->reference, $this->designation);
    }

    public function getPrixTTC(): float
    {
        return $this->prixUnitaire * (1 + ($this->tva ?? 0.2));
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}