<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class BalanceRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $id;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
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
