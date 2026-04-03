<?php

namespace App\Entity;

use App\Enum\Status_Announcement;
use App\Enum\Type_Unit;
use App\Repository\AnnouncementsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnnouncementsRepository::class)]
class Announcements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    #[Groups(['announcements:read'])]
    private ?Users $id_user = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'id_buyer', referencedColumnName: 'id', nullable: true)]
    #[Groups(['announcements:read'])]
    private ?Users $id_buyer = null;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'announcements')]
    #[ORM\JoinColumn(name: 'id_category', referencedColumnName: 'id')]
    #[Groups(['announcements:read'])]
    private ?Categories $id_category = null;

    #[ORM\Column(length: 255)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?bool $is_paid = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?string $price = null;

    #[ORM\Column(enumType: Type_Unit::class)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?Type_Unit $price_unit = null;

    #[ORM\Column(enumType: Status_Announcement::class)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?Status_Announcement $status = null;

    #[ORM\Column]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?bool $is_active = null;

    #[ORM\Column]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?Users
    {
        return $this->id_user;
    }

    public function setIdUser(?Users $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
/*
    public function getIdBuyer(): ?Users
    {
        return $this->id_buyer;
    }

    public function setIdBuyer(?Users $id_buyer): static
    {
        $this->id_buyer = $id_buyer;

        return $this;
    }*/

    public function getIdCategory(): ?Categories
    {
        return $this->id_category;
    }

    public function setIdCategory(?Categories $id_category): static
    {
        $this->id_category = $id_category;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->is_paid;
    }

    public function setIsPaid(bool $is_paid): static
    {
        $this->is_paid = $is_paid;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceUnit(): ?Type_Unit
    {
        return $this->price_unit;
    }

    public function setPriceUnit(Type_Unit $price_unit): static
    {
        $this->price_unit = $price_unit;

        return $this;
    }

    public function getStatus(): ?Status_Announcement
    {
        return $this->status;
    }

    public function setStatus(Status_Announcement $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
