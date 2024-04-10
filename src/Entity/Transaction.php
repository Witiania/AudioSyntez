<?php

namespace App\Entity;

use App\Repository\VoicesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'transaction')]
#[ORM\HasLifecycleCallbacks]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'users', referencedColumnName: 'guid')]
    private Users $user;

    #[ORM\Column(type: 'json')]
    private array $specification;

    #[ORM\Column(type: 'string')]
    private string $type = 'audio_syntez';

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->setCreatedAt(new \DateTime());
    }

    #[ORM\PreFlush]
    public function preFlush(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt ?? new \DateTime();
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSpecification(): array
    {
        return $this->specification;
    }

    public function setSpecification(array $specification): self
    {
        $this->specification = $specification;

        return $this;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getVoice(): string
    {
        $data = $this->getSpecification();
       return $data['voice'];
    }

    public function getText(): string
    {
        $data = $this->getSpecification();
        return $data['text'];
    }

    public function getFullPrice(): int
    {
        $data = $this->getSpecification();
        return $data['full_price'];
    }
    public function setFullPrice(int $fullPrice): self
    {
        $data = $this->getSpecification();
        $data['full_price'] = $fullPrice;
        $this->setSpecification($data);

        return $this;
    }
}