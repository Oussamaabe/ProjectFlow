<?php
// src/Entity/Projet.php
namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: 'boolean')]
    private bool $isCompleted = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $documents = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $deadlines = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $priceList = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $invoices = null;

    #[ORM\OneToMany(targetEntity: Ressource::class, mappedBy: 'projet', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ressources;

    public function __construct()
    {
        $this->documents = [];
        $this->deadlines = [
            ['name' => 'Lancement', 'planned' => null, 'actual' => null, 'status' => 'planned'],
            ['name' => 'Phase intermédiaire', 'planned' => null, 'actual' => null, 'status' => 'planned'],
            ['name' => 'Livraison', 'planned' => null, 'actual' => null, 'status' => 'planned']
        ];
        $this->priceList = [];
        $this->invoices = [];
        $this->ressources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }
    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFinPrevue(): ?\DateTimeInterface
    {
        return $this->dateFinPrevue;
    }
    public function setDateFinPrevue(?\DateTimeInterface $dateFinPrevue): self
    {
        $this->dateFinPrevue = $dateFinPrevue;
        return $this;
    }

    public function getAffaire(): ?Affaire
    {
        return $this->affaire;
    }
    public function setAffaire(?Affaire $affaire): self
    {
        $this->affaire = $affaire;
        return $this;
    }

    public function setDeadlines(array $deadlines): self
    {
        $this->deadlines = $deadlines;
        return $this;
    }

    public function setDocuments(array $documents): self
    {
        $this->documents = $documents;
        return $this;
    }

    public function setPriceList(array $priceList): self
    {
        $this->priceList = $priceList;
        return $this;
    }

    public function setInvoices(array $invoices): self
    {
        $this->invoices = $invoices;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }
    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        if ($isCompleted && !$this->dateFinPrevue) {
            $this->dateFinPrevue = new \DateTime();
        }

        return $this;
    }

    // Documents management
    public function getDocuments(): array
    {
        return $this->documents ?? [];
    }
    public function addDocument(string $name, string $type, ?string $filePath = null): self
    {
        $this->documents[] = [
            'name' => $name,
            'type' => $type,
            'filePath' => $filePath,
            'date' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        return $this;
    }
    public function removeDocument(int $index): self
    {
        if (isset($this->documents[$index])) {
            unset($this->documents[$index]);
            $this->documents = array_values($this->documents);
        }
        return $this;
    }

    // Deadlines management
    public function getDeadlines(): array
    {
        return $this->deadlines ?? [];
    }
    public function updateDeadline(int $index, array $data): self
    {
        if (isset($this->deadlines[$index])) {
            $this->deadlines[$index] = array_merge($this->deadlines[$index], $data);

            if ($index === count($this->deadlines) - 1 && isset($data['actual']) && $data['actual']) {
                $this->setIsCompleted(true);
            }
        }
        return $this;
    }

    // Price list management
    public function getPriceList(): array
    {
        return $this->priceList ?? [];
    }
    public function addPriceListItem(array $item): self
    {
        if (!isset($item['total']) && isset($item['quantity']) && isset($item['unitPrice'])) {
            $item['total'] = $item['quantity'] * $item['unitPrice'];
        }
        $this->priceList[] = $item;
        return $this;
    }
    public function updatePriceListItem(int $index, array $item): self
    {
        if (isset($this->priceList[$index])) {
            if (isset($item['quantity']) || isset($item['unitPrice'])) {
                $quantity = $item['quantity'] ?? $this->priceList[$index]['quantity'];
                $unitPrice = $item['unitPrice'] ?? $this->priceList[$index]['unitPrice'];
                $item['total'] = $quantity * $unitPrice;
            }
            $this->priceList[$index] = array_merge($this->priceList[$index], $item);
        }
        return $this;
    }
    public function removePriceListItem(int $index): self
    {
        if (isset($this->priceList[$index])) {
            unset($this->priceList[$index]);
            $this->priceList = array_values($this->priceList);
        }
        return $this;
    }

    public function calculateTotalPrice(): float
    {
        $priceList = $this->priceList ?? [];

        return array_reduce($priceList, function ($carry, $item) {
            $quantity = $item['quantity'] ?? 0;
            $unitPrice = $item['unitPrice'] ?? 0;
            $total = $item['total'] ?? ($quantity * $unitPrice);

            return $carry + $total;
        }, 0);
    }

    // Invoices management
    public function getInvoices(): array
    {
        return $this->invoices ?? [];
    }
    public function addInvoice(array $invoice): self
    {
        $this->invoices[] = $invoice;
        return $this;
    }
    public function updateInvoice(int $index, array $invoice): self
    {
        if (isset($this->invoices[$index])) {
            $this->invoices[$index] = array_merge($this->invoices[$index], $invoice);
        }
        return $this;
    }
    public function removeInvoice(int $index): self
    {
        if (isset($this->invoices[$index])) {
            unset($this->invoices[$index]);
            $this->invoices = array_values($this->invoices);
        }
        return $this;
    }

    // Ressources management
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources[] = $ressource;
            $ressource->setProjet($this);
        }
        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->removeElement($ressource)) {
            if ($ressource->getProjet() === $this) {
                $ressource->setProjet(null);
            }
        }
        return $this;
    }

    public function calculateTotalInvoiced(): float
    {
        $invoices = $this->invoices ?? [];

        return array_reduce($invoices, function ($carry, $invoice) {
            return $carry + ($invoice['amount'] ?? 0);
        }, 0);
    }

    public function __sleep()
    {
        return ['id', 'nom', 'description', 'dateDebut', 'dateFinPrevue', 'isCompleted'];
    }
}