<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFinPrevue = null;

    #[ORM\OneToOne(inversedBy: 'projet', targetEntity: Affaire::class)]
    #[ORM\JoinColumn(name: 'affaire_id', referencedColumnName: 'id')]
    private ?Affaire $affaire = null;

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): self { $this->dateDebut = $dateDebut; return $this; }
    public function getDateFinPrevue(): ?\DateTimeInterface { return $this->dateFinPrevue; }
    public function setDateFinPrevue(?\DateTimeInterface $dateFinPrevue): self { $this->dateFinPrevue = $dateFinPrevue; return $this; }
    public function getAffaire(): ?Affaire { return $this->affaire; }
    public function setAffaire(?Affaire $affaire): self
    {
        $this->affaire = $affaire;
        return $this;
    }
}