<?php

namespace App\DTO;
use App\Entity\Users;
use App\Entity\Voices;
use Symfony\Component\Validator\Constraints as Assert;
class OrderDto implements DTOResolverInterface
{
    #[Assert\NotBlank(message: 'text cannot be empty.')]
    #[Assert\Type(type: 'string', message: 'Text\'s value {{ value }} is not a string.')]
    #[Assert\NotIdenticalTo(value: 0, message: 'Text cannot be a zero.')]
    private string $text;

    #[Assert\NotBlank(message: 'Voices cannot be empty.')]
    #[Assert\Type(type: 'App\Entity\Voices', message: 'Voices\'s value {{ value }} is not a Voices entity.')]
    private Voices $voice;

    #[Assert\NotBlank(message: 'User cannot be empty.')]
    #[Assert\Type(type: 'App\Entity\Users', message: 'User\'s value {{ value }} is not a Users entity.')]
    private Users $user;

    public function getVoice():Voices
    {
        return $this->voice;
    }
    public function setVoice(Voices $voice): self
    {
        $this->voice = $voice;
        return $this;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;
        return $this;
    }
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