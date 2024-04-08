<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
class OrderDto implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'text cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Text\'s value {{ value }} is not a string.')]
    #[Assert\NotIdenticalTo(value: 0, message: 'Text cannot be a zero.')]
    private string $text;



    public function getText(): string
    {
        return $this->text;
    }
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
}