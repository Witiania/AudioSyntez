<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class ResetPasswordRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'Email cannot be empty.')]
    #[Assert\Email(message: 'Email\'s value does not fit.')]
    private string $email;

    #[Assert\NotCompromisedPassword(message: 'Password is compromised.')]
    #[PasswordStrength(
        ['minScore' => PasswordStrength::STRENGTH_WEAK],
        message: 'Password\'s value does not fit.'
    )]
    private string $password;

    #[Assert\NotBlank(message: 'Token cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Token\'s value {{ value }} is not a string.')]
    #[Assert\Length(exactly: 6, exactMessage: 'Token can only be 6 characters.')]
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
