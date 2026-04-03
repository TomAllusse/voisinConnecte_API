<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['categories:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 7)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?string $color_hex = null;

    #[ORM\Column(length: 50)]
    #[Groups(['categories:read', 'announcements:read'])]
    private ?string $icon = null;

    #[ORM\OneToMany(targetEntity: Announcements::class, mappedBy: 'id_category')]
    private Collection $announcements;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColorHex(): ?string
    {
        return $this->color_hex;
    }

    public function setColorHex(string $color_hex): static
    {
        $this->color_hex = $color_hex;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getAnnouncements(): Collection
    {
        return $this->announcements;
    }
}
