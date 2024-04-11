<?php
//
//namespace App\DTO;
//
//use App\Entity\Users;
//use Symfony\Component\Validator\Constraints as Assert;
//
//class TransactionDTO implements DTOResolverInterface
//{
//    #[Assert\NotBlank(message: 'Name cannot be empty.')]
//    #[Assert\Type(type: Users::class, message: 'Specification\'s value {{ value }} is not a string.')]
//    private array $specification;
//
//    #[Assert\NotBlank(message: 'Name cannot be empty.')]
//    #[Assert\Type(type: "string", message: 'User\'s value {{ value }} is not a string.')]
//    private string $type = 'audio_syntez';
//
//    public function getUser():Users
//    {
//
//    }
//    }