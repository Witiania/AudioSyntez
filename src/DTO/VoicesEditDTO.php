<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
class VoicesEditDTO implements DTOResolverInterface
{
    #[Assert\Type(type: 'string', message: 'Voice\'s value {{ value }} is not a string.')]
    private ?string $voice;

    #[Assert\Type(type: 'integer', message: 'Price\'s value {{ value }} is not an integer.')]
    private ?int $price;

    /**
     * @return string|null
     */
    public function getVoice(): ?string
    {
        return $this->voice;
    }

    public function setVoice(?string $voice): self
    {
        $this->voice = $voice;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;
        return $this;
    }
}