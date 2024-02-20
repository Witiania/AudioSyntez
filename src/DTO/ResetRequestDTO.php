<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class ResetRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[PasswordStrength(['minScore' => PasswordStrength::STRENGTH_WEAK])]
    private string $password;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $token;

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
