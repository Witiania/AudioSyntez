<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class RegistrationRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'Email cannot be empty.')]
    #[Assert\Regex('/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/')]
    private string $email;

    #[Assert\NotBlank(message: 'Name cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Name\'s value {{ value }} is not a string.')]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: 'Name must be not less than {{ limit }} characters.',
        maxMessage: 'Name must be not more than {{ limit }} characters.'
    )]
    private string $name;

    #[Assert\NotCompromisedPassword(message: 'Password is compromised.')]
    #[PasswordStrength(
        ['minScore' => PasswordStrength::STRENGTH_WEAK],
        message: 'Password\'s value does not fit.'
    )]
    private string $password;

    #[Assert\NotBlank(message: 'Phone cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Phone\'s value {{ value }} is not a string.')]
    #[Assert\Regex('/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/', message: 'Phone\'s value does not fit.')]
    private string $phone;

    #[Assert\IsTrue(
        message: 'Password cannot match your first name or email.'
    )]
    public function isPasswordSafe(): bool
    {
        return $this->password !== $this->name && $this->password !== $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
