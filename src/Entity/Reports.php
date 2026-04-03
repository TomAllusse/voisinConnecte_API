<?php

namespace App\Entity;

use App\Enum\Report_Reason;
use App\Repository\ReportsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportsRepository::class)]
class Reports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Announcements::class)]
    #[ORM\JoinColumn(name: 'id_announcement', referencedColumnName: 'id')]
    private ?Announcements $id_announcement = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'id_reporter', referencedColumnName: 'id')]
    private ?Users $id_reporter = null;

    #[ORM\Column(enumType: Report_Reason::class)]
    private ?Report_Reason $reason = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?bool $resolved = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'resolved_by', referencedColumnName: 'id')]
    private ?Users $resolved_by = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAnnouncement(): ?Announcements
    {
        return $this->id_announcement;
    }

    public function setIdAnnouncement(?Announcements $id_announcement): static
    {
        $this->id_announcement = $id_announcement;

        return $this;
    }

    public function getIdReporter(): ?Users
    {
        return $this->id_reporter;
    }

    public function setIdReporter(?Users $id_reporter): static
    {
        $this->id_reporter = $id_reporter;

        return $this;
    }

    public function getReason(): ?Report_Reason
    {
        return $this->reason;
    }

    public function setReason(Report_Reason $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function isResolved(): ?bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): static
    {
        $this->resolved = $resolved;

        return $this;
    }

    public function getResolvedBy(): ?Users
    {
        return $this->resolved_by;
    }

    public function setResolvedBy(?Users $resolved_by): static
    {
        $this->resolved_by = $resolved_by;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
