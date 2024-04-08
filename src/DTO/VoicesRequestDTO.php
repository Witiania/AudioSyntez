<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class VoicesRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'Name cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Voice\'s value {{ value }} is not a string.')]
    private string $voice;

    #[Assert\NotBlank(message: 'Price cannot be empty.')]
    #[Assert\Type(type: 'integer', message: 'Price\'s value {{ value }} is not an integer.')]
    #[Assert\NotIdenticalTo(value: 0, message: 'Amount cannot be a zero.')]
    private int $price;

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