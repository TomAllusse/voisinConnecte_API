<?php

namespace App\Entity;

use App\Enum\Status_Response;
use App\Repository\ResponsesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsesRepository::class)]
class Responses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_announcement = null;

    #[ORM\Column]
    private ?int $id_responder = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(enumType: Status_Response::class)]
    private ?Status_Response $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAnnouncement(): ?int
    {
        return $this->id_announcement;
    }

    public function setIdAnnouncement(int $id_announcement): static
    {
        $this->id_announcement = $id_announcement;

        return $this;
    }

    public function getIdResponder(): ?int
    {
        return $this->id_responder;
    }

    public function setIdResponder(int $id_responder): static
    {
        $this->id_responder = $id_responder;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?Status_Response
    {
        return $this->status;
    }

    public function setStatus(Status_Response $status): static
    {
        $this->status = $status;

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
