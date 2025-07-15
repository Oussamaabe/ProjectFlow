<?php

// src/Entity/Fournisseur.php
namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nom;

    #[ORM\Column(length: 100)]
    private string $email;

    #[ORM\Column(length: 20)]
    private string $telephone;

    #[ORM\Column(type: 'text')]
    private string $adresse;

    #[ORM\Column(length: 100)]
    private string $pays;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $identifiantFiscal = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    // Getters & Setters

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getAdresse(): string { return $this->adresse; }
    public function setAdresse(string $adresse): self { $this->adresse = $adresse; return $this; }

    public function getPays(): string { return $this->pays; }
    public function setPays(string $pays): self { $this->pays = $pays; return $this; }

    public function getIdentifiantFiscal(): ?string { return $this->identifiantFiscal; }
    public function setIdentifiantFiscal(?string $identifiantFiscal): self { $this->identifiantFiscal = $identifiantFiscal; return $this; }

    public function getDateCreation(): \DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }
}
