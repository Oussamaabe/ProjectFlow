<?php
namespace App\Entity;

use App\Repository\LigneCommandeAchatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeAchatRepository::class)]
class LigneCommandeAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CommandeAchat::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CommandeAchat $commande = null;

    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Column(type: 'integer')]
    private int $quantite;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $prixUnitaire;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total;

    // Getters & Setters

    public function getId(): ?int { return $this->id; }

    public function getCommande(): ?CommandeAchat { return $this->commande; }
    public function setCommande(?CommandeAchat $commande): self { $this->commande = $commande; return $this; }

    public function getArticle(): ?Article { return $this->article; }
    public function setArticle(?Article $article): self { $this->article = $article; return $this; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): self { $this->quantite = $quantite; return $this; }

    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prixUnitaire): self { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getTotal(): float { return $this->total; }
    public function setTotal(float $total): self { $this->total = $total; return $this; }
}
