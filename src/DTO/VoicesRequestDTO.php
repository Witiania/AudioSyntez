<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class VoicesRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'Name cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Voices\'s value {{ value }} is not a string.')]
    private string $voice;

    #[Assert\NotBlank(message: 'Price cannot be empty.')]
    #[Assert\Type(type: 'integer', message: 'Price\'s value {{ value }} is not an integer.')]
    #[Assert\NotIdenticalTo(value: 0, message: 'Amount cannot be a zero.')]
    private int $price;

    #[Assert\NotBlank(message: 'Format cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'format\'s value {{ value }} is not a string.')]
    private string $format = "oggopus";

    #[Assert\NotBlank(message: 'Gender cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'gender\'s value {{ value }} is not a string.')]
    private string $gender;

    #[Assert\NotBlank(message: 'Language cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Language\'s value {{ value }} is not a string.')]
    private string $language;



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

    public function getFormat(): string
    {
        return $this->format;
    }
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }
    public function getGender(): string
    {
        return $this->gender;
    }
    public function setGender(string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }
    public function getLanguage(): string
    {
        return $this->language;
    }
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }



}