<?php
// src/Entity/AuditLog.php
namespace App\Entity;

use App\Repository\AuditLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    private string $action;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $timestamp;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ipAddress = null;

    public function __construct()
    {
        $this->timestamp = new \DateTimeImmutable();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAction(): string { return $this->action; }
    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getDetails(): ?string { return $this->details; }
    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }

    public function getTimestamp(): \DateTimeInterface { return $this->timestamp; }

    public function getIpAddress(): ?string { return $this->ipAddress; }
    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }
}