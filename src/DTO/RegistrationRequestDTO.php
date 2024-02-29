<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class RegistrationRequestDTO implements DTOResolverInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 20)]
    private string $name;

    #[Assert\NotCompromisedPassword]
    #[PasswordStrength(['minScore' => PasswordStrength::STRENGTH_WEAK])]
    private string $password;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Regex('/\+?[1-9][0-9]{3}[0-9]{7}/', message: 'The phone number is not valid.')]
    private string $phone;

    #[Assert\IsTrue(
        message: 'The password cannot match your first name.'
    )]
    public function isPasswordSafe(): bool
    {
        return $this->password !== $this->name;
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
