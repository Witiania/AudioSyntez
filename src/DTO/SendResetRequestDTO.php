<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class SendResetRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
