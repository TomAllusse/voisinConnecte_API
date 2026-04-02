<?php

namespace App\Entity;

use App\Repository\RefreshTokensRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefreshTokensRepository::class)]
class RefreshTokens
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?Users $id_user = null;

    #[ORM\Column(length: 512)]
    private ?string $token_hash = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expired_at = null;

    #[ORM\Column]
    private ?bool $revoked = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

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

    public function getTokenHash(): ?string
    {
        return $this->token_hash;
    }

    public function setTokenHash(string $token_hash): static
    {
        $this->token_hash = $token_hash;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expired_at;
    }

    public function setExpiredAt(\DateTimeImmutable $expired_at): static
    {
        $this->expired_at = $expired_at;

        return $this;
    }

    public function isRevoked(): ?bool
    {
        return $this->revoked;
    }

    public function setRevoked(bool $revoked): static
    {
        $this->revoked = $revoked;

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
