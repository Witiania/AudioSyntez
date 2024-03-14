<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class VerifyEmailRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'Email cannot be empty.')]
    #[Assert\Email(message: 'Email\'s value does not fit.')]
    private string $email;

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
