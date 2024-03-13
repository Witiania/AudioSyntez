<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new Users())
            ->setName('admin')
            ->setEmail('admin@mail.ru')
            ->setToken('374445')
            ->setPhone('+79999999999')
            ->setRole('ROLE_ADMIN')
            ->setWallet(new Wallet());

        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'admin@mail.ru123'));
        $user->getWallet()->setBalance(8000);

        $user2 = new Users();
        $user2->setName('test')
            ->setEmail('test@mail.ru')
            ->setToken('666666')
            ->setPhone('+79999999994')
            ->setVerified(true)
            ->setWallet(new Wallet());

        $user2->setPassword($this->userPasswordHasher->hashPassword($user, 'test@mail.ru123'));
        $user2->getWallet()->setBalance(10000);

        $manager->persist($user);
        $manager->persist($user2);

        $manager->persist($user->getWallet());
        $manager->persist($user2->getWallet());
        $manager->flush();
    }
}
