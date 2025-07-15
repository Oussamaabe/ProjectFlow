<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'stock', targetEntity: Article::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $quantite = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $seuilAlerte = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    // Getters & Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getSeuilAlerte(): ?int
    {
        return $this->seuilAlerte;
    }

    public function setSeuilAlerte(?int $seuilAlerte): self
    {
        $this->seuilAlerte = $seuilAlerte;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
}
