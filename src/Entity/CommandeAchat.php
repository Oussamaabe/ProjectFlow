<?php
namespace App\Entity;

use App\Repository\CommandeAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeAchatRepository::class)]
class CommandeAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $numero;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCommande;

    #[ORM\Column(length: 50)]
    private string $statut;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total;

    #[ORM\ManyToOne(targetEntity: Fournisseur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommandeAchat::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
    }

    // Getters & Setters

    public function getId(): ?int { return $this->id; }

    public function getNumero(): string { return $this->numero; }
    public function setNumero(string $numero): self { $this->numero = $numero; return $this; }

    public function getDateCommande(): \DateTimeInterface { return $this->dateCommande; }
    public function setDateCommande(\DateTimeInterface $dateCommande): self { $this->dateCommande = $dateCommande; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getTotal(): float { return $this->total; }
    public function setTotal(float $total): self { $this->total = $total; return $this; }

    public function getFournisseur(): ?Fournisseur { return $this->fournisseur; }
    public function setFournisseur(?Fournisseur $fournisseur): self { $this->fournisseur = $fournisseur; return $this; }

    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(LigneCommandeAchat $ligne): self {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes[] = $ligne;
            $ligne->setCommande($this);
        }
        return $this;
    }

    public function removeLigne(LigneCommandeAchat $ligne): self {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
        }
        return $this;
    }
}
