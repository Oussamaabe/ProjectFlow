<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\DocumentSociete;

#[ORM\Entity(repositoryClass: SocieteRepository::class)]
class Societe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raisonSociale = null;

    #[ORM\Column]
    private ?float $capital = null;

    #[ORM\Column(length: 255)]
    private ?string $rc = null;

    #[ORM\Column(length: 255)]
    private ?string $tp = null;

    #[ORM\Column(length: 255)]
    private ?string $ifFiscal = null;

    #[ORM\Column(length: 255)]
    private ?string $cnss = null;

    #[ORM\Column(length: 255)]
    private ?string $ice = null;

    #[ORM\Column(length: 255)]
    private ?string $banque = null;

    #[ORM\Column(length: 255)]
    private ?string $rib = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteWeb = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'societe', targetEntity: DocumentSociete::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): self
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getCapital(): ?float
    {
        return $this->capital;
    }

    public function setCapital(float $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getRc(): ?string
    {
        return $this->rc;
    }

    public function setRc(string $rc): self
    {
        $this->rc = $rc;

        return $this;
    }

    public function getTp(): ?string
    {
        return $this->tp;
    }

    public function setTp(string $tp): self
    {
        $this->tp = $tp;

        return $this;
    }

    public function getIfFiscal(): ?string
    {
        return $this->ifFiscal;
    }

    public function setIfFiscal(string $ifFiscal): self
    {
        $this->ifFiscal = $ifFiscal;

        return $this;
    }

    public function getCnss(): ?string
    {
        return $this->cnss;
    }

    public function setCnss(string $cnss): self
    {
        $this->cnss = $cnss;

        return $this;
    }

    public function getIce(): ?string
    {
        return $this->ice;
    }

    public function setIce(string $ice): self
    {
        $this->ice = $ice;

        return $this;
    }

    public function getBanque(): ?string
    {
        return $this->banque;
    }

    public function setBanque(string $banque): self
    {
        $this->banque = $banque;

        return $this;
    }

    public function getRib(): ?string
    {
        return $this->rib;
    }

    public function setRib(string $rib): self
    {
        $this->rib = $rib;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(DocumentSociete $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setSociete($this);
        }

        return $this;
    }

    public function removeDocument(DocumentSociete $document): self
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getSociete() === $this) {
                $document->setSociete(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->raisonSociale ?? '';
    }
}