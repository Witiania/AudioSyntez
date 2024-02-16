<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ResetRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Assert\PasswordStrength]
    #[Assert\Length(min: 5)]
    private string $newPassword;

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

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
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
