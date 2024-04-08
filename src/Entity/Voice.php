<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'voices')]
class Voice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $text;

    #[ORM\Column(type: 'integer')]
    private int $FullPrice = 0;

    #[ORM\ManyToOne(targetEntity: ListVoices::class)]
    #[ORM\JoinColumn(name: 'voice', referencedColumnName: 'id', unique: true, nullable: false)]
    private ListVoices $voice;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $format = 'mp3';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getVoice(): ListVoices
    {
        return $this->voice;
    }

    public function setVoice(ListVoices $voice): self
    {
        $this->voice = $voice;

        return $this;
    }

    public function getFullPrice(): int
    {
        return $this->voice->getPrice() * mb_strlen($this->text);
    }
}
