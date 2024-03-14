<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class BalanceRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'Id cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Id\'s value {{ value }} is not a string.')]
    private string $id;

    #[Assert\NotBlank(message: 'Amount cannot be empty.')]
    #[Assert\Type(type: 'integer', message: 'Amount\'s value {{ value }} is not an integer.')]
    #[Assert\NotIdenticalTo(value: 0, message: 'Amount cannot be a zero.')]
    private int $amount;

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
