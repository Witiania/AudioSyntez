<?php

namespace App\Tests\E2E\DataFixture;

use App\Entity\Users;
use App\Entity\Wallet;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class UsersFixture
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public static function create(ContainerInterface $container, string $email, string $phone): Users
    {
        $user = (new Users())
            ->setName('Anatoliy')
            ->setPhone($phone)
            ->setEmail($email)
            ->setWallet(new Wallet())
            ->setToken('999999')
            ->setRole('ROLE_ADMIN');
        $user->setPassword($container->get('security.user_password_hasher')->hashPassword($user, '!1_Password'));

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->persist($user->getWallet());
        $manager->flush();

        return $user;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function destruct(ContainerInterface $container, string $email): void
    {
        $manager = $container
            ->get('doctrine')
            ->getManager();

        $user = $manager
            ->getRepository(Users::class)
            ->findOneBy(['email' => $email]);

        $manager->remove($user);
        $manager->remove($user->getWallet());
        $manager->flush();
    }
}
