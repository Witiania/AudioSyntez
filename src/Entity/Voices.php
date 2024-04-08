<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'voices')]
class Voices
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(name: 'id', type: Types::GUID, unique: true, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $voice;

    #[ORM\Column(type: 'integer', length: 255, nullable: false)]
    private int $price;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $format = 'mp3';

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getVoice(): string
    {
        return $this->voice;
    }

    public function setVoice(string $voice): self
    {
        $this->voice = $voice;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
